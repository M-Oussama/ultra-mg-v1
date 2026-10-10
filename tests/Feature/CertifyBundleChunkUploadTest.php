<?php

namespace Tests\Feature;

use App\Http\Controllers\CertifyInvoiceController;
use App\Jobs\ProcessCertifyBundleImport;
use App\Services\CertifyBundleChunkUpload;
use App\Services\CertifyBundleImportStateStore;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CertifyBundleChunkUploadTest extends TestCase
{
    private array $operations = [];

    protected function tearDown(): void
    {
        foreach ($this->operations as $id) {
            app(CertifyBundleImportStateStore::class)->forget($id);
            app(CertifyBundleChunkUpload::class)->cleanup($id);
            File::delete(storage_path('app/certify-import-queue/'.$id.'.zip'));
        }
        parent::tearDown();
    }

    private function request(array $fields, int $userId = 17): Request
    {
        $request = Request::create('/api/certifyInvoices/import-bundle', 'POST', $fields);
        $request->setUserResolver(fn () => (object) ['id' => $userId]);

        return $request;
    }

    private function prepare(string $bytes): array
    {
        $id = (string) Str::uuid();
        $this->operations[] = $id;
        $fields = [
            'operation_id' => $id, 'archive_size_bytes' => strlen($bytes),
            'archive_sha256' => hash('sha256', $bytes), 'chunked' => true,
        ];
        $response = app(CertifyInvoiceController::class)->prepareBundleImport($this->request($fields));
        $this->assertSame(200, $response->status());
        $this->assertSame(3, $response->getData(true)['protocol_version']);

        return [$id, $fields, $response->getData(true)];
    }

    private function sendChunk(string $id, int $index, string $bytes, int $userId = 17, ?string $hash = null): \Illuminate\Http\JsonResponse
    {
        $request = $this->request([
            'operation_id' => $id, 'chunk_index' => $index,
            'chunk_sha256' => $hash ?? hash('sha256', $bytes),
        ], $userId);
        $request->files->set('chunk', UploadedFile::fake()->createWithContent($index.'.part', $bytes));

        return app(CertifyInvoiceController::class)->uploadBundleChunk($request);
    }

    private function sendRawChunk(string $id, int $index, string $bytes, int $userId = 17, ?string $hash = null): \Illuminate\Http\JsonResponse
    {
        $request = Request::create('/api/certifyInvoices/import-bundle/chunk/raw', 'POST', [
            'operation_id' => $id, 'chunk_index' => $index,
            'chunk_sha256' => $hash ?? hash('sha256', $bytes),
        ], [], [], ['CONTENT_TYPE' => 'application/octet-stream'], $bytes);
        $request->setUserResolver(fn () => (object) ['id' => $userId]);

        return app(CertifyInvoiceController::class)->uploadBundleRawChunk($request);
    }

    public function test_chunks_resume_reassemble_exactly_and_finalize_without_a_large_request(): void
    {
        Bus::fake();
        $bytes = str_repeat('export-', 60000);
        [$id, $fields, $prepared] = $this->prepare($bytes);
        $size = $prepared['chunk_size_bytes'];
        $this->assertSame(200, $this->sendChunk($id, 0, substr($bytes, 0, $size))->status());
        $this->assertSame(200, $this->sendChunk($id, 0, substr($bytes, 0, $size))->status());
        $resumed = app(CertifyInvoiceController::class)->prepareBundleImport($this->request($fields))->getData(true);
        $this->assertSame([0], $resumed['received_chunks']);
        $this->assertSame(200, $this->sendChunk($id, 1, substr($bytes, $size))->status());
        $controller = app(CertifyInvoiceController::class);
        $request = $this->request(['operation_id' => $id]);
        $this->assertSame(202, $controller->startBundleImport($request)->status());
        $this->assertSame(202, $controller->startBundleImport($request)->status());
        Bus::assertDispatchedAfterResponseTimes(ProcessCertifyBundleImport::class, 1);
        $destination = storage_path('app/certify-import-queue/'.$id.'.zip');
        $state = app(CertifyBundleImportStateStore::class)->get($id);
        app(CertifyBundleChunkUpload::class)->assemble($id, $state['upload'], $destination);
        $this->assertSame(hash('sha256', $bytes), hash_file('sha256', $destination));
    }

    public function test_raw_chunk_fallback_mixes_with_multipart_and_keeps_checksum_guards(): void
    {
        $bytes = str_repeat('media-export-', 30000);
        [$id, $fields, $prepared] = $this->prepare($bytes);
        $size = $prepared['chunk_size_bytes'];
        $this->assertSame(200, $this->sendChunk($id, 0, substr($bytes, 0, $size))->status());
        $this->assertSame(200, $this->sendRawChunk($id, 1, substr($bytes, $size))->status());
        $this->assertSame(404, $this->sendRawChunk($id, 1, substr($bytes, $size), 18)->status());
        try {
            $this->sendRawChunk($id, 1, substr($bytes, $size), 17, str_repeat('0', 64));
            $this->fail('A corrupt raw chunk must be rejected');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('chunk_sha256', $exception->errors());
        }
        $resumed = app(CertifyInvoiceController::class)->prepareBundleImport($this->request($fields))->getData(true);
        $this->assertSame([0, 1], $resumed['received_chunks']);
        $destination = storage_path('app/certify-import-queue/'.$id.'.zip');
        $state = app(CertifyBundleImportStateStore::class)->get($id);
        app(CertifyBundleChunkUpload::class)->assemble($id, $state['upload'], $destination);
        $this->assertSame(hash('sha256', $bytes), hash_file('sha256', $destination));
    }

    public function test_incomplete_upload_cannot_start_a_job(): void
    {
        Bus::fake();
        [$id] = $this->prepare('incomplete export');
        try {
            app(CertifyInvoiceController::class)->startBundleImport($this->request(['operation_id' => $id]));
            $this->fail('An incomplete archive must not start');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('missing', $exception->errors()['chunk'][0]);
        }
        Bus::assertNotDispatchedAfterResponse(ProcessCertifyBundleImport::class);
    }

    public function test_chunk_ownership_and_checksum_are_enforced(): void
    {
        [$id] = $this->prepare('export');
        $this->assertSame(404, $this->sendChunk($id, 0, 'export', 18)->status());
        try {
            $this->sendChunk($id, 0, 'export', 17, str_repeat('0', 64));
            $this->fail('A corrupt chunk must be rejected');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('chunk_sha256', $exception->errors());
        }
        $this->assertSame([], app(CertifyBundleImportStateStore::class)->get($id)['upload']['received_chunks']);
    }

    public function test_a_changed_archive_cannot_reuse_uploaded_parts(): void
    {
        [$id, $fields] = $this->prepare('original');
        $fields['archive_sha256'] = hash('sha256', 'different');
        $this->assertSame(409, app(CertifyInvoiceController::class)->prepareBundleImport($this->request($fields))->status());
    }

    public function test_prepare_negotiates_small_requests_for_a_fifty_mb_archive(): void
    {
        $id = (string) Str::uuid();
        $this->operations[] = $id;
        $response = app(CertifyInvoiceController::class)->prepareBundleImport($this->request([
            'operation_id' => $id, 'chunked' => true,
            'archive_size_bytes' => 50 * 1048576, 'archive_sha256' => str_repeat('0', 64),
        ]));
        $this->assertSame(200, $response->status());
        $this->assertSame(3, $response->getData(true)['protocol_version']);
        $this->assertLessThanOrEqual(262144, $response->getData(true)['chunk_size_bytes']);
    }

    public function test_a_fifty_mb_archive_is_assembled_with_bounded_memory(): void
    {
        $id = (string) Str::uuid();
        $this->operations[] = $id;
        $service = app(CertifyBundleChunkUpload::class);
        $size = CertifyBundleChunkUpload::CHUNK_BYTES;
        $upload = ['archive_size_bytes' => 50 * 1048576, 'chunk_size_bytes' => $size, 'received_chunks' => []];
        $wholeHash = hash_init('sha256');
        for ($index = 0; $index < 200; $index++) {
            $bytes = str_repeat(chr($index % 256), $size);
            hash_update($wholeHash, $bytes);
            $upload = $service->receive($id, $upload, $index,
                UploadedFile::fake()->createWithContent($index.'.part', $bytes), hash('sha256', $bytes));
        }
        $upload['archive_sha256'] = hash_final($wholeHash);
        $destination = storage_path('app/certify-import-queue/'.$id.'.zip');
        $service->assemble($id, $upload, $destination);
        $this->assertSame(50 * 1048576, filesize($destination));
        $this->assertSame($upload['archive_sha256'], hash_file('sha256', $destination));
    }

    public function test_pruning_keeps_active_uploads_and_only_removes_expired_staging(): void
    {
        [$active] = $this->prepare('export');
        $service = app(CertifyBundleChunkUpload::class);
        $this->sendChunk($active, 0, 'export');
        $expired = (string) Str::uuid();
        $this->operations[] = $expired;
        File::ensureDirectoryExists($service->directory($expired));
        touch($service->directory($expired), now()->subDays(2)->timestamp);
        touch($service->directory($active), now()->subDays(2)->timestamp);
        $this->assertGreaterThanOrEqual(1, $service->pruneExpired());
        $this->assertFalse(is_dir($service->directory($expired)));
        $this->assertTrue(is_dir($service->directory($active)));
    }

    public function test_chunk_route_preserves_auth_permission_and_upload_throttling(): void
    {
        $route = app('router')->getRoutes()->getByName('uploadCertifyInvoiceBundleChunk');
        $middleware = app('router')->gatherRouteMiddleware($route);
        $this->assertContains(\App\Http\Middleware\Authenticate::class.':sanctum', $middleware);
        $this->assertContains(\App\Http\Middleware\CheckPermission::class.':add,certify_invoices', $middleware);
        $this->assertContains(\Illuminate\Routing\Middleware\ThrottleRequests::class.':certify-import-upload', $middleware);
        $this->assertNotContains(\Illuminate\Routing\Middleware\ThrottleRequests::class.':api', $middleware);
    }
}
