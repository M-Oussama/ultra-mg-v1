<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreImportOperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reference' => ['required', 'string', 'max:80', 'unique:import_operations,reference'],
            'legacy_importation_invoice_id' => ['nullable', 'integer', 'exists:importation_invoices,id'],
            'exporter_supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'currency' => ['required', 'string', 'size:3'],
            'origin' => ['nullable', 'string', 'max:120'],
            'destination' => ['nullable', 'string', 'max:120'],
            'status' => ['sometimes', Rule::in(['draft', 'active', 'partially_shipped', 'completed', 'cancelled'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
