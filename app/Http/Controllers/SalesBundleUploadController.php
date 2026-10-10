<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesBundleUploadController extends BundleUploadController
{
    protected function importKind(): string
    {
        return 'sales';
    }

    protected function importContext(Request $request): array
    {
        $validated = $request->validate([
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        return ['department_id' => (int) $validated['department_id']];
    }
}
