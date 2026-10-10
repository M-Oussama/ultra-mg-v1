<?php

namespace App\Services;

use Illuminate\Http\Request;

class BundleImportProgress
{
    public function update(Request $request, string $kind, float $progress, string $message): void
    {
        $id = $request->input('operation_id');
        if (! is_string($id) || $id === '') {
            return;
        }
        $store = app(CertifyBundleImportStateStore::class);
        $status = $store->get($id);
        if (! is_array($status)
            || ($status['kind'] ?? 'certify_data') !== $kind
            || (string) ($status['user_id'] ?? '') !== (string) optional($request->user())->id) {
            return;
        }
        $store->put($id, array_merge($status, [
            'status' => 'processing',
            'progress' => $progress,
            'message' => $message,
            'updated_at' => now()->timestamp,
        ]));
    }
}
