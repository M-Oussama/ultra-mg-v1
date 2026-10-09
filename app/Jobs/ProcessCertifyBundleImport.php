<?php

namespace App\Jobs;

use App\Http\Controllers\CertifyInvoiceController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
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
        @set_time_limit(0);
        $key = 'certify-bundle-import:'.$this->operationId;
        $status = Cache::get($key, []);
        Cache::put($key, array_merge($status, [
            'status' => 'processing',
            'progress' => 0.02,
            'message' => 'Opening the archive...',
        ]), now()->addDay());

        try {
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
                Cache::put($key, [
                    'operation_id' => $this->operationId,
                    'user_id' => $this->userId,
                    'status' => 'completed',
                    'progress' => 1.0,
                    'message' => 'Import completed',
                    'result' => $result,
                ], now()->addDay());
            } else {
                Cache::put($key, [
                    'operation_id' => $this->operationId,
                    'user_id' => $this->userId,
                    'status' => 'failed',
                    'progress' => 1.0,
                    'message' => $result['message'] ?? 'The import failed.',
                ], now()->addDay());
            }
        } catch (\Throwable $exception) {
            report($exception);
            Cache::put($key, [
                'operation_id' => $this->operationId,
                'user_id' => $this->userId,
                'status' => 'failed',
                'progress' => 1.0,
                'message' => 'Bundle import failed: '.$exception->getMessage(),
            ], now()->addDay());
        } finally {
            File::delete($this->bundlePath);
        }
    }
}
