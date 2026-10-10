<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCertifyBundleImport;
use App\Services\CertifyBundleChunkUpload;
use App\Services\CertifyBundleImportStateStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use ZipArchive;

/** Shared upload transport. Subclasses bind operations to a trusted import kind. */
class BundleUploadController extends Controller
{
    protected function importKind(): string
    {
        return 'certify_data';
    }

    protected function importContext(Request $request): array
    {
        return [];
    }

    public function prepareBundleImport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'operation_id' => 'required|uuid',
            'archive_size_bytes' => 'required|integer|min:1',
            'chunked' => 'sometimes|boolean',
            'archive_sha256' => [Rule::requiredIf($request->boolean('chunked')), 'regex:/^[a-f0-9]{64}$/'],
        ]);
        $context = $this->importContext($request);
        $validated['archive_size_bytes'] = (int) $validated['archive_size_bytes'];
        if (! class_exists(ZipArchive::class)) {
            return response()->json(['message' => 'The production server needs the PHP ZIP extension enabled.'], 422);
        }
        $fileLimit = $this->phpUploadLimitBytes((string) ini_get('upload_max_filesize'));
        $postLimit = $this->phpUploadLimitBytes((string) ini_get('post_max_size'));
        $chunked = $request->boolean('chunked');
        if ($chunked && $validated['archive_size_bytes'] > CertifyBundleChunkUpload::MAX_ARCHIVE_BYTES) {
            return response()->json(['message' => 'The maximum resumable archive size is 512 MB.'], 413);
        }
        $chunkSize = min(CertifyBundleChunkUpload::CHUNK_BYTES,
            $fileLimit > 0 ? $fileLimit : PHP_INT_MAX,
            $postLimit > 0 ? max(0, $postLimit - 65536) : PHP_INT_MAX);
        if (($chunked && $chunkSize < 16384)
            || (! $chunked && (($fileLimit > 0 && $validated['archive_size_bytes'] > $fileLimit)
                || ($postLimit > 0 && $postLimit < $validated['archive_size_bytes'] + 65536)))) {
            return response()->json([
                'message' => 'The archive exceeds this server\'s upload limits (upload_max_filesize='.ini_get('upload_max_filesize').', post_max_size='.ini_get('post_max_size').'). Increase these PHP limits on the production server.',
            ], 413);
        }

        $operationId = $validated['operation_id'];
        $store = app(CertifyBundleImportStateStore::class);
        try {
            $directory = storage_path('app/certify-import-queue');
            File::ensureDirectoryExists($directory);
            if (! is_writable($directory)) {
                throw new \RuntimeException('The server import folder is not writable. Make storage/app writable by PHP.');
            }
            $status = $store->withUploadLock($operationId, function () use ($store, $operationId, $request, $chunked, $validated, $chunkSize, $context): array|JsonResponse {
                $status = $store->get($operationId);
                if ($status !== null && ((string) ($status['user_id'] ?? '') !== (string) optional($request->user())->id
                    || ($status['kind'] ?? 'certify_data') !== $this->importKind())) {
                    return response()->json(['message' => 'Import operation not found.'], 404);
                }
                if ($status === null) {
                    $status = [
                        'operation_id' => $operationId,
                        'user_id' => optional($request->user())->id,
                        'kind' => $this->importKind(),
                        'context' => $context,
                        'status' => 'awaiting_upload',
                        'progress' => 0.0,
                        'message' => 'The server is ready to receive the archive.',
                        'updated_at' => now()->timestamp,
                    ];
                    $store->put($operationId, $status);
                }
                if (($status['context'] ?? []) !== $context) {
                    return response()->json(['message' => 'This request belongs to a different target department.'], 409);
                }
                if ($chunked && $status['status'] === 'awaiting_upload') {
                    if (isset($status['upload'])
                        && ($status['upload']['archive_size_bytes'] !== $validated['archive_size_bytes']
                            || $status['upload']['archive_sha256'] !== $validated['archive_sha256'])) {
                        return response()->json(['message' => 'This request belongs to a different archive. Select the original ZIP.'], 409);
                    }
                    $status['upload'] ??= [
                        'archive_size_bytes' => $validated['archive_size_bytes'],
                        'archive_sha256' => $validated['archive_sha256'],
                        'chunk_size_bytes' => $chunkSize,
                        'received_chunks' => [],
                    ];
                    $store->put($operationId, $status);
                }
                if ($store->get($operationId) === null) {
                    throw new \RuntimeException('The server cannot read saved import status. Check storage/app permissions.');
                }
                $status['confirmed_indices'] = isset($status['upload']) && $status['status'] === 'awaiting_upload'
                    ? app(CertifyBundleChunkUpload::class)->receivedIndices($operationId, $status['upload']) : [];

                return $status;
            });
            if ($status instanceof JsonResponse) {
                return $status;
            }
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json(['message' => $exception->getMessage()], 503);
        }

        return response()->json([
            'operation_id' => $operationId,
            'status' => $status['status'],
            'protocol_version' => $chunked ? 3 : 2,
            'chunk_size_bytes' => $status['upload']['chunk_size_bytes'] ?? $chunkSize,
            'received_chunks' => $status['confirmed_indices'],
        ]);
    }

    public function uploadBundleChunk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'operation_id' => 'required|uuid',
            'chunk_index' => 'required|integer|min:0',
            'chunk_sha256' => 'required|regex:/^[a-f0-9]{64}$/',
            'chunk' => 'required|file|max:256',
        ]);
        $operationId = $validated['operation_id'];
        $store = app(CertifyBundleImportStateStore::class);

        return $store->withUploadLock($operationId, function () use ($request, $validated, $operationId, $store): JsonResponse {
            $status = $store->get($operationId);
            if ($status === null || (string) $status['user_id'] !== (string) optional($request->user())->id
                || ($status['kind'] ?? 'certify_data') !== $this->importKind()) {
                return response()->json(['message' => 'Import operation not found.'], 404);
            }
            if ($status['status'] === 'awaiting_upload') {
                if (! isset($status['upload'])) {
                    return response()->json(['message' => 'Prepare the resumable upload first.'], 409);
                }
                $status['upload'] = app(CertifyBundleChunkUpload::class)->receive(
                    $operationId, $status['upload'], (int) $validated['chunk_index'],
                    $request->file('chunk'), $validated['chunk_sha256'],
                );
                $status['updated_at'] = now()->timestamp;
                $store->put($operationId, $status);
            }

            return response()->json([
                'operation_id' => $operationId,
                'status' => $status['status'],
                'chunk_index' => (int) $validated['chunk_index'],
            ]);
        });
    }

    private function phpUploadLimitBytes(string $value): int
    {
        $value = trim($value);
        $size = (int) $value;

        return $size * match (strtolower(substr($value, -1))) {
            'g' => 1073741824,
            'm' => 1048576,
            'k' => 1024,
            default => 1,
        };
    }

    public function startBundleImport(Request $request): JsonResponse
    {
        $request->validate(['operation_id' => 'sometimes|required|uuid']);
        $file = $request->file('bundle') ?? $request->file('file');
        if ($file && strtolower($file->getClientOriginalExtension()) !== 'zip') {
            return response()->json(['message' => 'A ZIP bundle file is required.'], 422);
        }

        $operationId = $request->input('operation_id') ?? (string) Str::uuid();
        $userId = optional($request->user())->id;
        $store = app(CertifyBundleImportStateStore::class);

        return $store->withUploadLock($operationId, function () use ($operationId, $userId, $store, $file): JsonResponse {
            $existing = $store->get($operationId);
            if (is_array($existing)) {
                if ((string) ($existing['user_id'] ?? '') !== (string) $userId
                    || ($existing['kind'] ?? 'certify_data') !== $this->importKind()) {
                    return response()->json(['message' => 'Import operation not found.'], 404);
                }

                if ($existing['status'] !== 'awaiting_upload') {
                    return response()->json([
                        'operation_id' => $operationId,
                        'status' => $existing['status'],
                    ], 202);
                }
            }
            if ($this->importKind() === 'sales' && ! isset($existing['context']['department_id'])) {
                return response()->json(['message' => 'Prepare the sales upload with a target department first.'], 422);
            }
            $directory = storage_path('app/certify-import-queue');
            File::ensureDirectoryExists($directory);
            $bundlePath = $directory.DIRECTORY_SEPARATOR.$operationId.'.zip';
            if ($file) {
                $file->move($directory, basename($bundlePath));
                unset($existing['upload']);
            } elseif (isset($existing['upload'])) {
                app(CertifyBundleChunkUpload::class)->assertComplete($operationId, $existing['upload']);
            } else {
                return response()->json(['message' => 'A ZIP bundle file is required.'], 422);
            }
            $store->put($operationId, array_merge($existing ?? [], [
                'operation_id' => $operationId,
                'user_id' => $userId,
                'kind' => $this->importKind(),
                'status' => 'queued',
                'progress' => 0.0,
                'message' => 'Upload complete. Waiting for server processing...',
                'updated_at' => now()->timestamp,
            ]));

            Bus::dispatchAfterResponse(new ProcessCertifyBundleImport(
                $operationId,
                $bundlePath,
                $userId,
                $this->importKind(),
                $existing['context'] ?? [],
            ));

            $response = response()->json([
                'operation_id' => $operationId,
                'status' => 'queued',
            ], 202);
            // Send a complete response before running the terminating callback.
            // LiteSpeed's scoped noabort rule lives in public/.htaccess.
            $response->headers->set('Content-Length', (string) strlen($response->getContent()));

            return $response;
        });
    }

    public function bundleImportStatus(Request $request, string $operationId): JsonResponse
    {
        $store = app(CertifyBundleImportStateStore::class);
        $status = $store->get($operationId);
        if (! is_array($status)
            || (string) ($status['user_id'] ?? '') !== (string) optional($request->user())->id
            || ($status['kind'] ?? 'certify_data') !== $this->importKind()) {
            return response()->json(['message' => 'Import operation not found.'], 404);
        }

        if (($status['status'] ?? '') === 'queued'
            && now()->timestamp - ($status['updated_at'] ?? now()->timestamp) > 120) {
            $status['status'] = 'failed';
            $status['message'] = 'The server did not start the import. No data was imported. Check the server PHP error log.';
            $store->put($operationId, $status);
        }

        unset($status['user_id'], $status['context'], $status['upload']);

        return response()->json($status);
    }
}
