<?php

namespace App\Http\Controllers;

use App\Models\ProductCostComponent;
use App\Models\ProductCostComponentAssignment;
use App\Models\ProductCostComponentPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductCostComponentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('search', ''));
        $departmentId = $request->input('department_id');

        $components = ProductCostComponent::with([
            'prices',
            'assignments.product:id,name,department_id',
        ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($departmentId !== null && $departmentId !== '', function ($query) use ($departmentId) {
                $query->whereHas('assignments.product', function ($productQuery) use ($departmentId) {
                    $productQuery->where('department_id', (int) $departmentId);
                });
            })
            ->orderBy('name')
            ->get();

        return response()->json(['components' => $components]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateComponentPayload($request);

        $component = DB::transaction(function () use ($validated) {
            $component = ProductCostComponent::create([
                'name' => $this->normalizeComponentName($validated),
                'cost_type' => $validated['cost_type'] ?? 'extra',
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->syncPrices($component, $validated['prices'] ?? []);
            $this->syncAssignments($component, $validated['assignments'] ?? []);

            return $component;
        });

        $component->load(['prices', 'assignments.product:id,name,department_id']);

        return response()->json([
            'message' => 'Cost component created successfully',
            'component' => $component,
        ]);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $component = ProductCostComponent::findOrFail($id);
        $validated = $this->validateComponentPayload($request);

        DB::transaction(function () use ($component, $validated) {
            $component->update([
                'name' => $this->normalizeComponentName($validated),
                'cost_type' => $validated['cost_type'] ?? 'extra',
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->syncPrices($component, $validated['prices'] ?? []);
            $this->syncAssignments($component, $validated['assignments'] ?? []);
        });

        $component->refresh()->load(['prices', 'assignments.product:id,name,department_id']);

        return response()->json([
            'message' => 'Cost component updated successfully',
            'component' => $component,
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        $component = ProductCostComponent::findOrFail($id);
        DB::transaction(function () use ($component) {
            $component->assignments()->delete();
            $component->prices()->delete();
            $component->delete();
        });

        return response()->json(['message' => 'Cost component deleted successfully']);
    }

    private function validateComponentPayload(Request $request): array
    {
        return $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'cost_type' => ['nullable', 'string', 'in:extra,raw_material'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'prices' => ['sometimes', 'array'],
            'prices.*.id' => ['nullable', 'integer', 'exists:product_cost_component_prices,id'],
            'prices.*.amount' => ['required_with:prices', 'numeric', 'min:0'],
            'prices.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'prices.*.effective_from' => ['nullable', 'date'],
            'prices.*.effective_to' => ['nullable', 'date'],
            'prices.*.notes' => ['nullable', 'string', 'max:1000'],
            'assignments' => ['sometimes', 'array'],
            'assignments.*.product_id' => ['required_with:assignments', 'integer', 'exists:products,id'],
            'assignments.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'assignments.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function syncPrices(ProductCostComponent $component, array $prices): void
    {
        $seenIds = [];

        foreach ($prices as $row) {
            if (!is_array($row)) {
                continue;
            }

            $validated = Validator::make($row, [
                'id' => ['nullable', 'integer', 'exists:product_cost_component_prices,id'],
                'amount' => ['required', 'numeric', 'min:0'],
                'quantity' => ['nullable', 'numeric', 'min:0'],
                'effective_from' => ['nullable', 'date'],
                'effective_to' => ['nullable', 'date'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ])->validate();

            $payload = [
                'amount' => round((float) $validated['amount'], 3),
                'quantity' => array_key_exists('quantity', $validated) && $validated['quantity'] !== null
                    ? round((float) $validated['quantity'], 3)
                    : null,
                'effective_from' => $validated['effective_from'] ?? null,
                'effective_to' => $validated['effective_to'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ];

            $price = null;
            if (!empty($validated['id'])) {
                $price = $component->prices()->whereKey((int) $validated['id'])->first();
            }

            if ($price) {
                $price->update($payload);
                $seenIds[] = $price->id;
                continue;
            }

            $created = $component->prices()->create($payload);
            $seenIds[] = $created->id;
        }

        if (!empty($seenIds)) {
            $component->prices()->whereNotIn('id', $seenIds)->delete();
        } else {
            $component->prices()->delete();
        }
    }

    private function syncAssignments(ProductCostComponent $component, array $assignments): void
    {
        $seenIds = [];

        foreach ($assignments as $row) {
            if (!is_array($row)) {
                continue;
            }

            $validated = Validator::make($row, [
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'quantity' => ['nullable', 'numeric', 'min:0'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ])->validate();

            $assignment = ProductCostComponentAssignment::updateOrCreate(
                [
                    'component_id' => $component->id,
                    'product_id' => (int) $validated['product_id'],
                ],
                [
                    'quantity' => round((float) ($validated['quantity'] ?? 1), 3),
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            $seenIds[] = $assignment->id;
        }

        if (!empty($seenIds)) {
            $component->assignments()->whereNotIn('id', $seenIds)->delete();
        } else {
            $component->assignments()->delete();
        }
    }

    private function normalizeComponentName(array $validated): string
    {
        $name = trim((string) ($validated['name'] ?? ''));
        if ($name !== '') {
            return $name;
        }

        return (($validated['cost_type'] ?? 'extra') === 'raw_material')
            ? 'Raw material component'
            : 'Shared cost component';
    }
}
