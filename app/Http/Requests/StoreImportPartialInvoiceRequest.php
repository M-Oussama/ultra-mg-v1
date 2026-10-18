<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImportPartialInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $allocationRequired = $this->route('partialId') !== null ? 'sometimes' : 'required';
        return [
            'number' => ['required', 'string', 'max:80'],
            'container_allocation_count' => [$allocationRequired, 'integer', 'min:1'],
            'freight_amount' => ['nullable', 'regex:/^\d{1,14}(\.\d{1,4})?$/'],
            'container_ids' => ['nullable', 'array'],
            'container_ids.*' => ['integer', 'exists:import_containers,id'],
            'idempotency_key' => ['nullable', 'string', 'max:100'],
            'lines' => [$allocationRequired, 'array', 'min:1'],
            'lines.*.parent_line_id' => ['required', 'integer', 'distinct'],
            'lines.*.quantity' => ['required', 'regex:/^\d{1,15}(\.\d{1,3})?$/', 'not_in:0,0.0,0.00,0.000'],
        ];
    }
}
