<?php

namespace App\Jobs;

use App\Http\Controllers\CertifyInvoiceController;
use App\Services\CertifyBundleImportStateStore;
use App\Services\CertifyBundleChunkUpload;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class ProcessCertifyBundleImport
{
    public function __construct(
        private readonly string $operationId,
        private readonly string $bundlePath,
        private readonly ?int $userId,
    ) {}

    public function handle(CertifyInvoiceController $controller): void
    {
        ignore_user_abort(true);
        @set_time_limit(0);
        $store = app(CertifyBundleImportStateStore::class);
        // Fatal PHP errors bypass catch (Throwable), including memory
        // exhaustion. Retain enough memory to record a definite failure.
        $reserve = str_repeat('x', 262144);
        $finished = false;
        register_shutdown_function(function () use ($store, &$reserve, &$finished): void {
            if ($finished) {
                return;
            }
            $reserve = null;
            $error = error_get_last();
            if (! $error || ! in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
                return;
            }
            $store->put($this->operationId, [
                'operation_id' => $this->operationId,
                'user_id' => $this->userId,
                'status' => 'failed',
                'progress' => 0.0,
                'message' => 'The server stopped the import: '.$error['message'],
                'updated_at' => now()->timestamp,
            ]);
        });
        $status = $store->get($this->operationId) ?? [];
        $store->put($this->operationId, array_merge($status, [
            'status' => 'processing',
            'progress' => 0.02,
            'message' => isset($status['upload']) ? 'Reassembling and verifying the uploaded archive...' : 'Opening the archive...',
            'updated_at' => now()->timestamp,
        ]));

        try {
            if (isset($status['upload'])) {
                app(CertifyBundleChunkUpload::class)->assemble($this->operationId, $status['upload'], $this->bundlePath);
            }
            $request = Request::create('/api/certifyInvoices/import-bundle', 'POST', [
                'operation_id' => $this->operationId,
            ]);
            $request->files->set('bundle', new UploadedFile(
                $this->bundlePath,
                basename($this->bundlePath),
                'application/zip',
                null,
                true,
            ));

            $response = $controller->importBundle($request);
            $result = $response->getData(true);
            if ($response->isSuccessful()) {
                $store->put($this->operationId, [
                    'operation_id' => $this->operationId,
                    'user_id' => $this->userId,
                    'status' => 'completed',
                    'progress' => 1.0,
                    'message' => 'Import completed',
                    'result' => $result,
                ]);
            } else {
                $store->put($this->operationId, [
                    'operation_id' => $this->operationId,
                    'user_id' => $this->userId,
                    'status' => 'failed',
                    'progress' => 1.0,
                    'message' => $result['message'] ?? 'The import failed.',
                ]);
            }
        } catch (\Throwable $exception) {
            report($exception);
            $store->put($this->operationId, [
                'operation_id' => $this->operationId,
                'user_id' => $this->userId,
                'status' => 'failed',
                'progress' => 1.0,
                'message' => 'Bundle import failed: '.$exception->getMessage(),
            ]);
        } finally {
            $finished = true;
            File::delete($this->bundlePath);
            app(CertifyBundleChunkUpload::class)->cleanup($this->operationId);
        }
    }
}
