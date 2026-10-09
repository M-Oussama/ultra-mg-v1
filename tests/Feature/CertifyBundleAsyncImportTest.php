<?php

namespace Tests\Feature;

use App\Http\Controllers\CertifyInvoiceController;
use App\Jobs\ProcessCertifyBundleImport;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CertifyBundleAsyncImportTest extends TestCase
{
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
        } finally {
            Cache::forget('certify-bundle-import:'.$operationId);
            File::delete(storage_path('app/certify-import-queue/'.$operationId.'.zip'));
        }
    }
}
