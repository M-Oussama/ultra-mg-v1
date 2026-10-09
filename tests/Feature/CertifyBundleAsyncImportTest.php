<?php

namespace Tests\Feature;

use App\Http\Controllers\CertifyInvoiceController;
use App\Jobs\ProcessCertifyBundleImport;
use App\Services\CertifyBundleImportStateStore;
use App\Services\CertifyBundleChunkUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;
use ZipArchive;

class CertifyBundleAsyncImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_prepare_status_survives_default_cache_flush_and_a_fresh_store(): void
    {
        $operationId = (string) Str::uuid();
        $request = Request::create('/api/certifyInvoices/import-bundle/prepare', 'POST', [
            'operation_id' => $operationId, 'archive_size_bytes' => 100,
        ]);
        $request->setUserResolver(fn () => (object) ['id' => 17]);
        try {
            $response = app(CertifyInvoiceController::class)->prepareBundleImport($request);
            $this->assertSame(200, $response->status());
            $this->assertSame(2, $response->getData(true)['protocol_version']);
            Cache::flush();
            $this->assertSame('awaiting_upload', (new CertifyBundleImportStateStore())->get($operationId)['status']);
            $this->assertSame('awaiting_upload', app(CertifyInvoiceController::class)
                ->bundleImportStatus($request, $operationId)->getData(true)['status']);
            $request->setUserResolver(fn () => (object) ['id' => 18]);
            $this->assertSame(404, app(CertifyInvoiceController::class)
                ->bundleImportStatus($request, $operationId)->status());
        } finally {
            app(CertifyBundleImportStateStore::class)->forget($operationId);
        }
    }

    public function test_prepare_rejects_an_archive_above_php_upload_limits(): void
    {
        $request = Request::create('/api/certifyInvoices/import-bundle/prepare', 'POST', [
            'operation_id' => (string) Str::uuid(), 'archive_size_bytes' => PHP_INT_MAX,
        ]);
        $request->setUserResolver(fn () => (object) ['id' => 17]);
        $response = app(CertifyInvoiceController::class)->prepareBundleImport($request);
        $this->assertSame(413, $response->status());
        $this->assertStringContainsString('upload_max_filesize', $response->getData(true)['message']);
    }

    public function test_prepare_reports_status_storage_failure_before_upload(): void
    {
        $this->app->bind(CertifyBundleImportStateStore::class, fn () => new class extends CertifyBundleImportStateStore
        {
            public function put(string $operationId, array $status): void
            {
                throw new \RuntimeException('The server cannot save import status. Make storage/app writable by PHP.');
            }
        });
        $request = Request::create('/api/certifyInvoices/import-bundle/prepare', 'POST', [
            'operation_id' => (string) Str::uuid(), 'archive_size_bytes' => 100,
        ]);
        $request->setUserResolver(fn () => (object) ['id' => 17]);
        $response = app(CertifyInvoiceController::class)->prepareBundleImport($request);
        $this->assertSame(503, $response->status());
        $this->assertStringContainsString('storage/app', $response->getData(true)['message']);
    }

    public function test_bundle_upload_returns_an_operation_before_processing(): void
    {
        Bus::fake();
        $user = new class
        {
            public int $id = 17;
        };
        $request = Request::create('/api/certifyInvoices/import-bundle/start', 'POST');
        $request->setUserResolver(fn () => $user);
        $request->files->set(
            'bundle',
            UploadedFile::fake()->create('certify.zip', 1, 'application/zip'),
        );

        $response = app(CertifyInvoiceController::class)->startBundleImport($request);
        $payload = $response->getData(true);
        $operationId = $payload['operation_id'];

        try {
            $this->assertSame(202, $response->status());
            $this->assertSame('queued', $payload['status']);
            Bus::assertDispatchedAfterResponse(ProcessCertifyBundleImport::class);

            $statusRequest = Request::create(
                '/api/certifyInvoices/import-bundle/status/'.$operationId,
                'GET',
            );
            $statusRequest->setUserResolver(fn () => $user);
            $status = app(CertifyInvoiceController::class)
                ->bundleImportStatus($statusRequest, $operationId);

            $this->assertSame(200, $status->status());
            $this->assertSame('queued', $status->getData(true)['status']);
            $request->merge(['operation_id' => $operationId]);
            $request->files->set('bundle', UploadedFile::fake()->create('retry.zip', 1, 'application/zip'));
            $retry = app(CertifyInvoiceController::class)->startBundleImport($request);
            $this->assertSame($operationId, $retry->getData(true)['operation_id']);
            Bus::assertDispatchedAfterResponseTimes(ProcessCertifyBundleImport::class, 1);
        } finally {
            app(CertifyBundleImportStateStore::class)->forget($operationId);
            File::delete(storage_path('app/certify-import-queue/'.$operationId.'.zip'));
        }
    }

    public function test_async_operation_commits_clients_products_invoices_and_items(): void
    {
        [$controller, $request, $operationId] = $this->startRealImport();
        $this->assertDatabaseCount('certify_clients', 0);

        // Execute the actual terminating callback registered by the upload,
        // rather than merely asserting that a job was dispatched.
        $this->app->terminate();
        $status = $controller->bundleImportStatus($request, $operationId)->getData(true);

        try {
            $this->assertSame('completed', $status['status'], $status['message']);
            $this->assertSame(1, $status['result']['counts']['clients']);
            $this->assertSame(1, $status['result']['counts']['products']);
            $this->assertSame(1, $status['result']['counts']['invoices']);
            $this->assertSame(1, $status['result']['counts']['invoice_products']);
            $this->assertDatabaseHas('certify_clients', ['id' => 150, 'name' => 'Amine']);
            $this->assertDatabaseHas('certify_invoices', ['id' => 501, 'client_id' => 150]);
            $this->assertDatabaseHas('certify_invoice_products', [
                'id' => 901, 'certify_invoice_id' => 501, 'product_id' => 25,
            ]);
        } finally {
            app(CertifyBundleImportStateStore::class)->forget($operationId);
        }
    }

    public function test_async_database_failure_is_reported_and_rolls_back_all_rows(): void
    {
        DB::unprepared("CREATE TRIGGER reject_bundle_products BEFORE INSERT ON certify_products BEGIN SELECT RAISE(ABORT, 'connection refused by destination database'); END");
        [$controller, $request, $operationId] = $this->startRealImport();
        $this->app->terminate();
        $status = $controller->bundleImportStatus($request, $operationId)->getData(true);

        try {
            $this->assertSame('failed', $status['status']);
            $this->assertStringContainsString('connection refused by destination database', $status['message']);
            $this->assertArrayNotHasKey('result', $status);
            $this->assertDatabaseCount('certify_clients', 0);
            $this->assertDatabaseCount('certify_products', 0);
            $this->assertDatabaseCount('certify_invoices', 0);
        } finally {
            app(CertifyBundleImportStateStore::class)->forget($operationId);
        }
    }

    public function test_chunked_archive_is_assembled_imported_and_temporary_parts_removed(): void
    {
        [$controller, $request, $operationId] = $this->startRealImport(true);
        try {
            $this->app->terminate();
            $status = $controller->bundleImportStatus($request, $operationId)->getData(true);
            $this->assertSame('completed', $status['status'], $status['message']);
            $this->assertDatabaseHas('certify_clients', ['id' => 150, 'name' => 'Amine']);
            $this->assertDatabaseHas('certify_invoices', ['id' => 501, 'client_id' => 150]);
            $this->assertDatabaseHas('certify_invoice_products', ['id' => 901, 'certify_invoice_id' => 501]);
            $this->assertFalse(is_dir(app(CertifyBundleChunkUpload::class)->directory($operationId)));
        } finally {
            app(CertifyBundleImportStateStore::class)->forget($operationId);
            app(CertifyBundleChunkUpload::class)->cleanup($operationId);
        }
    }

    public function test_bad_assembled_checksum_fails_before_any_rows_are_imported(): void
    {
        [$controller, $request, $operationId] = $this->startRealImport(true);
        $store = app(CertifyBundleImportStateStore::class);
        $state = $store->get($operationId);
        $state['upload']['archive_sha256'] = str_repeat('0', 64);
        $store->put($operationId, $state);
        try {
            $this->app->terminate();
            $status = $controller->bundleImportStatus($request, $operationId)->getData(true);
            $this->assertSame('failed', $status['status']);
            $this->assertStringContainsString('checksum', $status['message']);
            $this->assertDatabaseCount('certify_clients', 0);
            $this->assertDatabaseCount('certify_products', 0);
            $this->assertDatabaseCount('certify_invoices', 0);
        } finally {
            $store->forget($operationId);
            app(CertifyBundleChunkUpload::class)->cleanup($operationId);
        }
    }

    private function startRealImport(bool $chunked = false): array
    {
        $cityId = DB::table('cities')->insertGetId([
            'code' => 16, 'name' => 'Algiers', 'country' => 'Algeria',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $path = tempnam(sys_get_temp_dir(), 'certify-async-');
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        $zip->addFromString('clients.csv', "id,name,surname,city_id\n150,Amine,Saidi,{$cityId}\n");
        $zip->addFromString('products.csv', "id,name,price\n25,Invoice paper,100\n");
        $zip->addFromString('commands.csv', "id,fac_id,date,client_id,amount_ttc,amount,payment_type,tva\n501,9001,2026-10-09,150,200,200,1,0\n");
        $zip->addFromString('command_products.csv', "id,command_id,product_id,price,quantity,amount\n901,501,25,100,2,200\n");
        if ($chunked) {
            $zip->addFromString('padding.bin', str_repeat('legacy-export', 25000));
            $zip->setCompressionName('padding.bin', ZipArchive::CM_STORE);
        }
        $zip->close();
        $operationId = (string) Str::uuid();
        $request = Request::create('/api/certifyInvoices/import-bundle/start', 'POST', [
            'operation_id' => $operationId,
        ]);
        $request->setUserResolver(fn () => (object) ['id' => 17]);
        $request->files->set('bundle', new UploadedFile($path, 'certify.zip', 'application/zip', null, true));
        $controller = app(CertifyInvoiceController::class);
        $prepare = Request::create('/api/certifyInvoices/import-bundle/prepare', 'POST', [
            'operation_id' => $operationId, 'archive_size_bytes' => filesize($path),
            'chunked' => $chunked, 'archive_sha256' => hash_file('sha256', $path),
        ]);
        $prepare->setUserResolver(fn () => (object) ['id' => 17]);
        $prepared = $controller->prepareBundleImport($prepare);
        $this->assertSame(200, $prepared->status());
        if ($chunked) {
            $bytes = File::get($path);
            $size = $prepared->getData(true)['chunk_size_bytes'];
            for ($offset = 0, $index = 0; $offset < strlen($bytes); $offset += $size, $index++) {
                $part = substr($bytes, $offset, $size);
                $chunkRequest = Request::create('/api/certifyInvoices/import-bundle/chunk', 'POST', [
                    'operation_id' => $operationId, 'chunk_index' => $index,
                    'chunk_sha256' => hash('sha256', $part),
                ]);
                $chunkRequest->setUserResolver(fn () => (object) ['id' => 17]);
                $chunkRequest->files->set('chunk', UploadedFile::fake()->createWithContent($index.'.part', $part));
                $this->assertSame(200, $controller->uploadBundleChunk($chunkRequest)->status());
            }
            $request->files->remove('bundle');
            File::delete($path);
        }
        $response = $controller->startBundleImport($request);
        $this->assertSame(202, $response->status());
        $this->assertSame($operationId, $response->getData(true)['operation_id']);

        return [$controller, $request, $operationId];
    }

    public function test_real_legacy_archive_commits_all_supported_rows(): void
    {
        $fixture = getenv('CERTIFY_IMPORT_TEST_BUNDLE');
        if (! $fixture || ! is_file($fixture)) {
            $this->markTestSkipped('Set CERTIFY_IMPORT_TEST_BUNDLE to run the private export fixture.');
        }
        DB::table('cities')->insert([
            'id' => 1, 'code' => 1, 'name' => 'Adrar', 'country' => 'Algeria',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $path = tempnam(sys_get_temp_dir(), 'certify-real-');
        File::copy($fixture, $path);
        $operationId = (string) Str::uuid();
        $request = Request::create('/api/certifyInvoices/import-bundle/start', 'POST', [
            'operation_id' => $operationId,
        ]);
        $request->setUserResolver(fn () => (object) ['id' => 17]);
        $request->files->set('bundle', new UploadedFile($path, 'certify.zip', 'application/zip', null, true));
        $controller = app(CertifyInvoiceController::class);
        $this->assertSame(202, $controller->startBundleImport($request)->status());
        $this->app->terminate();
        $status = $controller->bundleImportStatus($request, $operationId)->getData(true);

        try {
            $this->assertSame('completed', $status['status'], $status['message']);
            $this->assertSame([], $status['result']['skipped']);
            $this->assertDatabaseCount('certify_clients', 160);
            $this->assertDatabaseCount('certify_products', 25);
            $this->assertDatabaseCount('certify_invoices', 397);
            $this->assertDatabaseCount('certify_invoice_products', 1323);
            $this->assertSame(37, $status['result']['counts']['cheques']);
        } finally {
            app(CertifyBundleImportStateStore::class)->forget($operationId);
        }
    }
}
