<?php

namespace App\Services;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;

class CertifyBundleImportStateStore
{
    private Repository $cache;

    public function __construct(?string $directory = null)
    {
        // Import status must survive separate PHP requests. The application
        // default cache may be array/null, or depend on an unavailable Redis
        // service on shared hosting. Keep it alongside the uploaded bundles.
        $this->cache = new Repository(new FileStore(
            new Filesystem(),
            $directory ?? storage_path('app/certify-import-status'),
        ));
    }

    public function get(string $operationId): ?array
    {
        $status = $this->cache->get($this->key($operationId));

        return is_array($status) ? $status : null;
    }

    public function put(string $operationId, array $status): void
    {
        if (! $this->cache->put($this->key($operationId), $status, now()->addDay())) {
            throw new \RuntimeException('The server cannot save import status. Make storage/app writable by PHP.');
        }
    }

    public function forget(string $operationId): void
    {
        $this->cache->forget($this->key($operationId));
    }

    public function withUploadLock(string $operationId, \Closure $callback): mixed
    {
        return $this->cache->getStore()->lock($this->key($operationId).':upload', 60)
            ->block(10, $callback);
    }

    private function key(string $operationId): string
    {
        return 'certify-bundle-import:'.$operationId;
    }
}
