<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImportParentInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sales_supplier_id' => ['nullable', 'integer', 'exists:sales_suppliers,id'],
            'number' => ['required', 'string', 'max:80'],
            'currency' => ['required', 'string', 'size:3'],
            'planned_container_count' => ['required', 'integer', 'min:1'],
            'freight_per_container' => ['nullable', 'regex:/^\d{1,14}(\.\d{1,4})?$/'],
            'show_container_price' => ['sometimes', 'boolean'],
            'container_ids' => ['nullable', 'array'],
            'container_ids.*' => ['integer', 'exists:import_containers,id'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.id' => ['nullable', 'integer'],
            'lines.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'lines.*.description' => ['required', 'string', 'max:255'],
            'lines.*.unit' => ['nullable', 'string', 'max:30'],
            'lines.*.quantity' => ['required', 'regex:/^\d{1,15}(\.\d{1,3})?$/', 'not_in:0,0.0,0.00,0.000'],
            'lines.*.commercial_unit_price' => ['required', 'regex:/^\d{1,14}(\.\d{1,4})?$/'],
        ];
    }
}
