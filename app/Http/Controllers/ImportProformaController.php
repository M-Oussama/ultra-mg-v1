<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ImportProforma;
use App\Models\Product;
use App\Models\SalesSupplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ImportProformaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'perPage' => 'nullable|integer|min:1|max:100',
            'currentPage' => 'nullable|integer|min:1',
            'departement_id' => 'nullable|integer|exists:departments,id',
            'supplier_id' => 'nullable|integer|exists:sales_suppliers,id',
            'search' => 'nullable|string|max:255',
        ]);

        $query = ImportProforma::query()
            ->with(['supplier', 'department', 'items'])
            ->latest('proforma_date')
            ->latest('id');

        if (! empty($validated['departement_id'])) {
            $query->where('departement_id', $validated['departement_id']);
        }
        if (! empty($validated['supplier_id'])) {
            $query->where('sales_supplier_id', $validated['supplier_id']);
        }
        if (! empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->where(function ($nested) use ($search) {
                $nested->where('number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                        $supplierQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items', function ($itemQuery) use ($search) {
                        $itemQuery->where('product_name', 'like', "%{$search}%")
                            ->orWhere('reference', 'like', "%{$search}%");
                    });
            });
        }

        $page = $query->paginate(
            $validated['perPage'] ?? 20,
            ['*'],
            'page',
            $validated['currentPage'] ?? 1
        );

        return response()->json([
            'success' => true,
            'data' => $page->items(),
            'currentPage' => $page->currentPage(),
            'totalPage' => $page->lastPage(),
            'total' => $page->total(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatedPayload($request);

        $proforma = DB::transaction(function () use ($validated, $request) {
            $totals = $this->calculateTotals($validated['items']);
            $proforma = ImportProforma::create([
                'number' => $this->uniqueNumber($validated['number'] ?? null),
                'proforma_date' => $validated['proforma_date'],
                'sales_supplier_id' => $validated['supplier_id'],
                'departement_id' => $validated['departement_id'],
                'created_by' => $request->user()?->id,
                'currency' => strtoupper($validated['currency']),
                'total_quantity' => $totals['quantity'],
                'total_amount' => $totals['amount'],
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'] ?? 'draft',
            ]);

            $this->replaceItems($proforma, $validated['items']);

            return $this->loadProforma($proforma);
        });

        return response()->json([
            'success' => true,
            'message' => 'Import proforma saved successfully.',
            'proforma' => $proforma,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'proforma' => $this->loadProforma(ImportProforma::findOrFail($id)),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $proforma = ImportProforma::findOrFail($id);
        $validated = $this->validatedPayload($request, $proforma);

        $proforma = DB::transaction(function () use ($validated, $proforma) {
            $totals = $this->calculateTotals($validated['items']);
            $proforma->update([
                'number' => $validated['number'] ?? $proforma->number,
                'proforma_date' => $validated['proforma_date'],
                'sales_supplier_id' => $validated['supplier_id'],
                'departement_id' => $validated['departement_id'],
                'currency' => strtoupper($validated['currency']),
                'total_quantity' => $totals['quantity'],
                'total_amount' => $totals['amount'],
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'] ?? $proforma->status,
            ]);

            $this->replaceItems($proforma, $validated['items']);

            return $this->loadProforma($proforma);
        });

        return response()->json([
            'success' => true,
            'message' => 'Import proforma updated successfully.',
            'proforma' => $proforma,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $proforma = ImportProforma::findOrFail($id);

        DB::transaction(function () use ($proforma) {
            $proforma->items()->delete();
            $proforma->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Import proforma deleted successfully.',
        ]);
    }

    private function validatedPayload(
        Request $request,
        ?ImportProforma $existing = null
    ): array {
        $payload = $request->input('data', $request->all());
        $numberRule = Rule::unique('import_proformas', 'number');
        if ($existing) {
            $numberRule->ignore($existing->id);
        }

        $validated = Validator::make($payload, [
            'number' => ['nullable', 'string', 'max:80', $numberRule],
            'proforma_date' => 'required|date',
            'supplier_id' => 'required|integer|exists:sales_suppliers,id',
            'departement_id' => 'required|integer|exists:departments,id',
            'currency' => 'required|string|size:3',
            'notes' => 'nullable|string|max:5000',
            'status' => 'nullable|string|in:draft,sent,accepted,cancelled',
            'items' => 'required|array|min:1|max:500',
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.reference' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|integer|min:1|max:999999999',
            'items.*.unit_price' => 'required|numeric|min:0|max:999999999999',
        ])->validate();

        $department = Department::findOrFail($validated['departement_id']);
        if ($department->is_production) {
            throw ValidationException::withMessages([
                'departement_id' => ['Import proformas are available only for resell departments.'],
            ]);
        }

        $supplier = SalesSupplier::findOrFail($validated['supplier_id']);
        if ((int) $supplier->departement_id !== (int) $department->id) {
            throw ValidationException::withMessages([
                'supplier_id' => ['The supplier does not belong to this department.'],
            ]);
        }

        $productIds = collect($validated['items'])
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();
        if ($productIds->isNotEmpty()) {
            $foreignProductExists = Product::query()
                ->whereIn('id', $productIds)
                ->whereNotNull('department_id')
                ->where('department_id', '!=', $department->id)
                ->exists();
            if ($foreignProductExists) {
                throw ValidationException::withMessages([
                    'items' => ['Every product must belong to the selected department.'],
                ]);
            }
        }

        return $validated;
    }

    private function calculateTotals(array $items): array
    {
        $quantity = 0;
        $amount = 0.0;
        foreach ($items as $item) {
            $quantity += (int) $item['quantity'];
            $amount += (int) $item['quantity'] * (float) $item['unit_price'];
        }

        return ['quantity' => $quantity, 'amount' => round($amount, 4)];
    }

    private function replaceItems(ImportProforma $proforma, array $items): void
    {
        $proforma->items()->delete();
        foreach ($items as $item) {
            $quantity = (int) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $proforma->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'product_name' => trim($item['product_name']),
                'reference' => isset($item['reference'])
                    ? trim((string) $item['reference'])
                    : null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => round($quantity * $unitPrice, 4),
            ]);
        }
    }

    private function uniqueNumber(?string $requested): string
    {
        $base = trim((string) $requested);
        if ($base === '') {
            $base = 'IMP-'.now()->format('Ymd-His');
        }
        $candidate = $base;
        $suffix = 2;
        while (ImportProforma::withTrashed()->where('number', $candidate)->exists()) {
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function loadProforma(ImportProforma $proforma): ImportProforma
    {
        return $proforma->fresh(['supplier', 'department', 'items.product']);
    }
}
