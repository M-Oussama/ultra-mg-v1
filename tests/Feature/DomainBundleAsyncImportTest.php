<?php

namespace Tests\Feature;

use App\Http\Controllers\CertifyInvoiceController;
use App\Http\Controllers\MediaBundleUploadController;
use App\Http\Controllers\SalesBundleUploadController;
use App\Models\CertifyClient;
use App\Models\Cheque;
use App\Models\Department;
use App\Models\Product;
use App\Models\User;
use App\Services\CertifyBundleChunkUpload;
use App\Services\CertifyBundleImportStateStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use ZipArchive;

class DomainBundleAsyncImportTest extends TestCase
{
    use RefreshDatabase;

    private array $operations = [];

    private array $paths = [];

    protected function tearDown(): void
    {
        foreach ($this->operations as $id) {
            app(CertifyBundleImportStateStore::class)->forget($id);
            app(CertifyBundleChunkUpload::class)->cleanup($id);
            File::delete(storage_path('app/certify-import-queue/'.$id.'.zip'));
        }
        foreach ($this->paths as $path) {
            File::delete($path);
        }
        parent::tearDown();
    }

    private function city(): int
    {
        return DB::table('cities')->insertGetId([
            'code' => 16, 'name' => 'Algiers', 'country' => 'Algeria',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function request(array $data, User $user): Request
    {
        $request = Request::create('/api/import-bundle', 'POST', $data);
        $request->setUserResolver(fn () => $user);

        return $request;
    }

    private function stage($controller, array $files, User $user, array $context = []): array
    {
        $path = tempnam(sys_get_temp_dir(), 'domain-import-');
        $this->paths[] = $path;
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        foreach ($files as $name => $content) {
            $zip->addFromString($name, $content);
        }
        // Force more than one chunk, including media/reference files.
        $zip->addFromString('padding.bin', str_repeat('export-padding', 25000));
        $zip->setCompressionName('padding.bin', ZipArchive::CM_STORE);
        $zip->close();
        $id = (string) Str::uuid();
        $this->operations[] = $id;
        $request = $this->request(array_merge($context, [
            'operation_id' => $id,
            'archive_size_bytes' => filesize($path),
            'archive_sha256' => hash_file('sha256', $path),
            'chunked' => true,
        ]), $user);
        $prepared = $controller->prepareBundleImport($request);
        $this->assertSame(200, $prepared->status(), $prepared->getContent());
        $size = $prepared->getData(true)['chunk_size_bytes'];
        $bytes = File::get($path);
        for ($offset = 0, $index = 0; $offset < strlen($bytes); $offset += $size, $index++) {
            $part = substr($bytes, $offset, $size);
            $chunk = $this->request([
                'operation_id' => $id, 'chunk_index' => $index,
                'chunk_sha256' => hash('sha256', $part),
            ], $user);
            $chunk->files->set('chunk', UploadedFile::fake()->createWithContent('part.bin', $part));
            $this->assertSame(200, $controller->uploadBundleChunk($chunk)->status());
        }
        $resumed = $controller->prepareBundleImport($request)->getData(true);
        $this->assertCount(2, $resumed['received_chunks']);

        return [$id, $this->request(['operation_id' => $id], $user)];
    }

    public function test_media_chunks_attach_client_and_cheque_files_without_importing_reference_rows(): void
    {
        Storage::fake('public');
        config(['media-library.disk_name' => 'public']);
        $user = User::factory()->create();
        $client = CertifyClient::create(['name' => 'Existing client', 'city_id' => $this->city()]);
        $cheque = Cheque::create([
            'cheque_date' => '2026-10-09', 'cheque_number' => 'CH-001',
            'client_id' => $client->id, 'amount' => 100,
        ]);
        $pdf = "%PDF-1.4\n% Example attachment\n%%EOF";
        $hash = hash('sha256', $pdf);
        $controller = app(MediaBundleUploadController::class);
        [$id, $request] = $this->stage($controller, [
            'manifest.json' => json_encode(['related_media' => [
                'metadata_file' => 'media.csv', 'files_directory' => 'media-files',
            ]]),
            'clients.csv' => "id,name\n{$client->id},Do not overwrite\n999,Do not create\n",
            'cheques.csv' => "id,cheque_number\n{$cheque->id},Do not overwrite\n999,Do not create\n",
            'media.csv' => "id,owner_table,owner_id,collection_name,relative_path,file_exported,file_name,sha256\n"
                ."1,clients,{$client->id},cnrc,media-files/client.pdf,yes,client.pdf,{$hash}\n"
                ."2,cheques,{$cheque->id},cheques,media-files/cheque.pdf,yes,cheque.pdf,{$hash}\n"
                ."3,clients,{$client->id},nif,media-files/missing.pdf,yes,missing.pdf,\n",
            'media-files/client.pdf' => $pdf, 'media-files/cheque.pdf' => $pdf,
        ], $user);
        $this->assertSame(202, $controller->startBundleImport($request)->status());
        $this->app->terminate();
        $state = $controller->bundleImportStatus($request, $id)->getData(true);
        $this->assertSame('completed', $state['status'], $state['message']);
        $this->assertSame(2, $state['result']['counts']['attached']);
        $this->assertSame(1, $state['result']['counts']['missing_files']);
        $this->assertDatabaseCount('certify_clients', 1);
        $this->assertDatabaseCount('cheques', 1);
        $this->assertSame('Existing client', $client->fresh()->name);
        $this->assertSame('CH-001', $cheque->fresh()->cheque_number);
        $this->assertDatabaseHas('media', [
            'model_id' => $client->id, 'model_type' => CertifyClient::class, 'collection_name' => 'cnrc_file',
        ]);
        $this->assertDatabaseHas('media', [
            'model_id' => $cheque->id, 'model_type' => Cheque::class, 'collection_name' => 'cheques',
        ]);
        $attachment = $client->fresh()->getFirstMedia('cnrc_file');
        $this->assertFileExists($attachment->getPath());
        $this->assertSame($hash, hash_file('sha256', $attachment->getPath()));
        $this->assertFalse(is_dir(app(CertifyBundleChunkUpload::class)->directory($id)));
        $this->assertArrayNotHasKey('upload', $state);
    }

    public function test_sales_chunks_preserve_department_user_payments_and_sales_returns(): void
    {
        $user = User::factory()->create();
        $department = Department::create(['name' => 'Sales import', 'department_type' => 'resell']);
        $product = Product::create([
            'name' => 'Container', 'price' => 100, 'tax_rate' => 0, 'department_id' => $department->id,
        ]);
        $city = $this->city();
        DB::table('sale_statuses')->insertOrIgnore([
            ['id' => 1, 'name' => 'Not paid'], ['id' => 2, 'name' => 'Paid'], ['id' => 3, 'name' => 'Partially paid'],
        ]);
        $controller = app(SalesBundleUploadController::class);
        [$id, $request] = $this->stage($controller, [
            'clients.csv' => "id,name,city_id\nlegacy-client,Amine,{$city}\n",
            'sales.csv' => "id,client_id,sale_date,total_amount\nlegacy-sale,legacy-client,2026-10-09,200\n",
            'sale_items.csv' => "id,sale_id,product_id,quantity,price\nlegacy-item,legacy-sale,{$product->id},2,100\n",
            'payments.csv' => "id,sale_id,client_id,amount_paid,payment_date\nlegacy-payment,legacy-sale,legacy-client,50,2026-10-09\n",
            'product_returns.csv' => "id,total_amount,client_id,date,paid\nlegacy-return,100,legacy-client,2026-10-09,0\n",
            'product_return_lists.csv' => "id,return_id,client_id,product_id,quantity,price,total_price,date\n"
                ."legacy-return-item,legacy-return,legacy-client,{$product->id},1,100,100,2026-10-09\n",
        ], $user, ['department_id' => $department->id]);
        $this->assertNull(Auth::user());
        $this->assertSame(202, $controller->startBundleImport($request)->status());
        // Repeated finalization must not dispatch another import.
        $this->assertSame(202, $controller->startBundleImport($request)->status());
        $this->app->terminate();
        $state = $controller->bundleImportStatus($request, $id)->getData(true);
        $this->assertSame('completed', $state['status'], $state['message']);
        $this->assertSame([], $state['result']['errors']);
        foreach (['clients_created', 'sales_created', 'sale_items_created', 'payments_created',
            'product_returns_created', 'product_return_items_created'] as $key) {
            $this->assertSame(1, $state['result']['counts'][$key], $key);
        }
        $this->assertDatabaseHas('sales', [
            'external_id' => 'legacy-sale', 'department_id' => $department->id,
            'user_id' => $user->id, 'paid_amount' => 50, 'balance' => 150,
        ]);
        $this->assertDatabaseHas('clients', ['name' => 'Amine', 'user_id' => $user->id, 'department_id' => $department->id]);
        $this->assertDatabaseHas('product_returns', ['department_id' => $department->id, 'total_amount' => 100]);
        $this->assertDatabaseCount('certify_clients', 0);
        $this->assertDatabaseCount('sales', 1);
        $this->assertNull(Auth::user());
        $this->assertFalse(is_dir(storage_path('app/certify-import-queue/'.$id.'-csv')));
        // A retry after completion must recover the result, not reject the
        // originally chosen department because terminal state lost context.
        $retry = $this->request([
            'operation_id' => $id, 'department_id' => $department->id,
            'archive_size_bytes' => 100, 'archive_sha256' => str_repeat('a', 64),
            'chunked' => true,
        ], $user);
        $prepared = $controller->prepareBundleImport($retry);
        $this->assertSame(200, $prepared->status());
        $this->assertSame('completed', $prepared->getData(true)['status']);
    }

    public function test_sales_database_failure_rolls_back_rows_and_reports_the_real_error(): void
    {
        $user = User::factory()->create();
        $department = Department::create(['name' => 'Sales import']);
        $city = $this->city();
        DB::table('sale_statuses')->insertOrIgnore(['id' => 1, 'name' => 'Not paid']);
        DB::unprepared("CREATE TRIGGER reject_import_sales BEFORE INSERT ON sales BEGIN SELECT RAISE(ABORT, 'sales database rejected import'); END");
        $controller = app(SalesBundleUploadController::class);
        [$id, $request] = $this->stage($controller, [
            'clients.csv' => "id,name,city_id\nlegacy-client,Amine,{$city}\n",
            'sales.csv' => "id,client_id,sale_date,total_amount\nlegacy-sale,legacy-client,2026-10-09,200\n",
        ], $user, ['department_id' => $department->id]);
        $this->assertSame(202, $controller->startBundleImport($request)->status());
        $this->app->terminate();
        $state = $controller->bundleImportStatus($request, $id)->getData(true);
        $this->assertSame('failed', $state['status']);
        $this->assertStringContainsString('sales database rejected import', $state['message']);
        $this->assertDatabaseCount('clients', 0);
        $this->assertDatabaseCount('sales', 0);
        $this->assertFalse(is_dir(storage_path('app/certify-import-queue/'.$id.'-csv')));
    }

    public function test_operations_are_bound_to_their_domain_and_owner(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $controller = app(MediaBundleUploadController::class);
        [$id, $request] = $this->stage($controller, ['clients.csv' => "id,name\n"], $user);
        foreach ([CertifyInvoiceController::class, SalesBundleUploadController::class] as $class) {
            $wrong = app($class);
            $this->assertSame(404, $wrong->bundleImportStatus($request, $id)->status());
            $this->assertSame(404, $wrong->startBundleImport($request)->status());
            $chunk = $this->request(['operation_id' => $id, 'chunk_index' => 0, 'chunk_sha256' => str_repeat('a', 64)], $user);
            $chunk->files->set('chunk', UploadedFile::fake()->createWithContent('chunk.bin', 'bad'));
            $this->assertSame(404, $wrong->uploadBundleChunk($chunk)->status());
        }
        $otherRequest = $this->request(['operation_id' => $id], $other);
        $this->assertSame(404, $controller->bundleImportStatus($otherRequest, $id)->status());
        $this->assertSame(404, $controller->startBundleImport($otherRequest)->status());
    }

    public function test_sales_resume_cannot_change_target_department(): void
    {
        $user = User::factory()->create();
        $department = Department::create(['name' => 'Original department']);
        $other = Department::create(['name' => 'Other department']);
        $controller = app(SalesBundleUploadController::class);
        [$id] = $this->stage($controller, ['sales.csv' => "id,total_amount\n"], $user, ['department_id' => $department->id]);
        $saved = app(CertifyBundleImportStateStore::class)->get($id);
        $resume = $this->request([
            'operation_id' => $id, 'department_id' => $other->id,
            'chunked' => true, 'archive_size_bytes' => $saved['upload']['archive_size_bytes'],
            'archive_sha256' => $saved['upload']['archive_sha256'],
        ], $user);
        $this->assertSame(409, $controller->prepareBundleImport($resume)->status());
        $this->assertSame($department->id, app(CertifyBundleImportStateStore::class)->get($id)['context']['department_id']);
    }

    public function test_new_chunk_routes_keep_auth_permissions_and_scoped_throttling(): void
    {
        foreach ([
            'api/certifyInvoices/import-media-bundle/chunk' => 'certify_invoices',
            'api/pos/sales/import-bundle/chunk' => 'sales',
        ] as $uri => $permission) {
            $route = collect(app('router')->getRoutes())->first(fn ($route) => $route->uri() === $uri);
            $middleware = app('router')->gatherRouteMiddleware($route);
            $joined = implode('|', $middleware);
            $this->assertStringContainsString('Authenticate:sanctum', $joined);
            $this->assertStringContainsString('CheckPermission:add,'.$permission, $joined);
            $this->assertStringContainsString('ThrottleRequests:certify-import-upload', $joined);
            $this->assertStringNotContainsString('ThrottleRequests:api', $joined);
        }
    }
}
