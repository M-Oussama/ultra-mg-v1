<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Department;
use App\Models\Product;
use App\Models\ProductReturnList;
use App\Models\SalesSupplier;
use App\Models\SaleItem;
use App\Models\Supply;
use App\Models\SupplyItem;
use App\Models\ProductStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyController extends Controller
{
    private function resolvedUnitsPerPackage(?Product $product): int
    {
        return (int) ($product?->units_per_package ?? 0);
    }

    private function packagingAvailabilityFromUnits(float $remainingQuantity, int $unitsPerPackage): array
    {
        if ($unitsPerPackage <= 0) {
            return [
                'available_cartons' => 0,
                'loose_quantity' => $remainingQuantity,
                'carton_size' => 0,
            ];
        }

        $availableCartons = (int) floor($remainingQuantity / $unitsPerPackage);

        return [
            'available_cartons' => $availableCartons,
            'loose_quantity' => $remainingQuantity - ($availableCartons * $unitsPerPackage),
            'carton_size' => $unitsPerPackage,
        ];
    }

    private function packagingAvailability(float $remainingQuantity, ?Product $product): array
    {
        return $this->packagingAvailabilityFromUnits(
            $remainingQuantity,
            $this->resolvedUnitsPerPackage($product)
        );
    }

    private function isFullPackageOrder(SaleItem $saleItem, float $quantity): bool
    {
        if (!$saleItem->hasPackaging()) {
            return false;
        }

        $unitsPerPackage = $this->resolvedUnitsPerPackage($saleItem->product);
        if ($unitsPerPackage <= 0) {
            return false;
        }

        $remainder = fmod($quantity, $unitsPerPackage);
        return abs($remainder) < 0.00001 || abs($remainder - $unitsPerPackage) < 0.00001;
    }

    private function allocateFromQueue(
        array &$queue,
        SaleItem $saleItem,
        float &$remainingToAllocate,
        string $allocationPreference = 'all'
    ): void {
        $unitsPerPackage = $this->resolvedUnitsPerPackage($saleItem->product);

        foreach ($queue as &$batch) {
            if ($remainingToAllocate <= 0) {
                break;
            }

            $availableQuantity = (float) ($batch->remaining_quantity ?? 0);
            if ($availableQuantity <= 0) {
                continue;
            }

            if ($allocationPreference === 'cartons') {
                if (!$saleItem->hasPackaging() || $unitsPerPackage <= 0) {
                    continue;
                }

                $availableQuantity = (float) (floor($availableQuantity / $unitsPerPackage) * $unitsPerPackage);
            } elseif ($allocationPreference === 'loose') {
                if (!$saleItem->hasPackaging() || $unitsPerPackage <= 0) {
                    continue;
                }

                $availableQuantity = fmod($availableQuantity, $unitsPerPackage);
            }

            if ($availableQuantity <= 0) {
                continue;
            }

            $consumed = min($availableQuantity, $remainingToAllocate);
            $batch->remaining_quantity -= $consumed;
            $batch->sold_quantity += $consumed;
            $remainingToAllocate -= $consumed;
        }

        unset($batch);
    }

    private function applySaleConsumption(array &$queue, SaleItem $saleItem): void
    {
        $remainingToAllocate = (float) $saleItem->quantity;

        if (!$saleItem->hasPackaging()) {
            $this->allocateFromQueue($queue, $saleItem, $remainingToAllocate, 'all');
            return;
        }

        if ($this->isFullPackageOrder($saleItem, (float) $saleItem->quantity)) {
            $this->allocateFromQueue($queue, $saleItem, $remainingToAllocate, 'cartons');
            if ($remainingToAllocate > 0) {
                $this->allocateFromQueue($queue, $saleItem, $remainingToAllocate, 'loose');
            }
            return;
        }

        $this->allocateFromQueue($queue, $saleItem, $remainingToAllocate, 'loose');

        if ($remainingToAllocate > 0) {
            $this->allocateFromQueue($queue, $saleItem, $remainingToAllocate, 'cartons');
        }
    }

    private function generateSupplyItemReference(int $productId, ?int $departmentId, array &$sequenceByKey): string
    {
        $key = ($departmentId ?? 'all') . ':' . $productId;

        if (!array_key_exists($key, $sequenceByKey)) {
            $query = SupplyItem::query()
                ->join('supplies', 'supplies.id', '=', 'supply_items.supply_id')
                ->where('supply_items.product_id', $productId);

            if ($departmentId !== null) {
                $query->where('supplies.departement_id', $departmentId);
            }

            $sequenceByKey[$key] = (int) $query
                ->pluck('supply_items.reference')
                ->map(static fn ($reference) => (int) $reference)
                ->max();
        }

        $sequenceByKey[$key]++;

        return str_pad((string) $sequenceByKey[$key], 4, '0', STR_PAD_LEFT);
    }

    private function resolveSupplyItemReference(array $item, int $productId, ?int $departmentId, array &$sequenceByKey): string
    {
        $manualReference = trim((string) ($item['reference'] ?? ''));
        if ($manualReference !== '') {
            return $manualReference;
        }

        return $this->generateSupplyItemReference($productId, $departmentId, $sequenceByKey);
    }

    /**
     * Get a paginated list of supplies (buying invoices).
     */
    public function getSupplies(Request $request): JsonResponse
    {
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $supplier_id = $request->input('supplier_id', '');
        $departement_id = $request->input('departement_id', '');
        $from = $request->input('from', '');
        $to = $request->input('to', '');

        $query = Supply::query();

        if ($supplier_id != '') {
            $query->where('sales_supplier_id', $supplier_id);
        }
        if ($departement_id != '') {
            $query->where('departement_id', $departement_id);
        }
        if ($from != '' && $to != '') {
            $query->whereBetween('supply_date', [$from, $to]);
        } else if ($from != '') {
            $query->where('supply_date', '>=', $from);
        }

        $query->orderBy('supply_date', 'desc');

        $paginatedResult = $query->paginate($perPage, ['*'], 'page', $currentPage);

        return response()->json([
            "supplies" => $paginatedResult->items(),
            "currentPage" => $paginatedResult->currentPage(),
            "totalPage" => $paginatedResult->lastPage(),
            "totalSupplies" => $paginatedResult->total()
        ]);
    }

    /**
     * Get data for initializing the supply form (suppliers, departments, products, etc.).
     */
    public function getData(): JsonResponse
    {
        $suppliers = SalesSupplier::all();
        $departments = Department::all();
        $products = Product::getAllProductsFormatted();
        $cities = City::all();

        return response()->json([
            "suppliers" => $suppliers,
            "departments" => $departments,
            "products" => $products,
            "cities" => $cities
        ]);
    }

    /**
     * Get container-level stock batches for a department.
     */
    public function getStockBatches(Request $request): JsonResponse
    {
        $departmentId = $request->input('department_id', '');
        $search = trim((string) $request->input('search', ''));

        $suppliesQuery = Supply::with(['supplier', 'department', 'items.product'])
            ->orderBy('supply_date')
            ->orderBy('id');

        if ($departmentId !== '') {
            $suppliesQuery->where('departement_id', $departmentId);
        }

        $supplies = $suppliesQuery->get();

        $eventsByProduct = [];
        $productsSummary = [];
        $containers = [];
        $totalReceived = 0.0;
        $totalSold = 0.0;

        foreach ($supplies as $supply) {
            $container = (object) [
                'id' => $supply->id,
                'reference' => 'SUP-' . $supply->id,
                'supply_date' => $supply->supply_date,
                'supplier_name' => $supply->supplier?->name ?? 'Supplier',
                'department_id' => $supply->departement_id,
                'department_name' => $supply->department?->name ?? null,
                'notes' => $supply->notes,
                'items' => [],
                'received_total' => 0.0,
                'sold_total' => 0.0,
                'remaining_total' => 0.0,
            ];

            foreach ($supply->items as $item) {
                $batch = (object) [
                    'source_type' => 'container',
                    'source_id' => $item->id,
                    'supply_id' => $supply->id,
                    'product_id' => $item->product_id,
                    'reference' => $item->reference,
                    'product_name' => $item->product?->name ?? 'Product',
                    'received_quantity' => (float) $item->quantity,
                    'sold_quantity' => 0.0,
                    'remaining_quantity' => (float) $item->quantity,
                    'available_cartons' => 0,
                    'loose_quantity' => 0.0,
                    'carton_size' => $this->resolvedUnitsPerPackage($item->product),
                    'unit_price' => (float) $item->unit_price,
                    'date' => $supply->supply_date,
                ];

                $containerAvailability = $this->packagingAvailability(
                    $batch->remaining_quantity,
                    $item->product
                );
                $batch->available_cartons = $containerAvailability['available_cartons'];
                $batch->loose_quantity = $containerAvailability['loose_quantity'];

                $container->items[] = $batch;

                $productId = (string) $item->product_id;
                $eventsByProduct[$productId][] = [
                    'type' => 'supply',
                    'date' => $supply->supply_date,
                    'order' => $item->id,
                    'batch' => $batch,
                ];

                if (!isset($productsSummary[$productId])) {
                    $productsSummary[$productId] = [
                        'product_id' => (string) $item->product_id,
                        'product_name' => $item->product?->name ?? 'Product',
                        'received_quantity' => 0.0,
                        'sold_quantity' => 0.0,
                        'remaining_quantity' => 0.0,
                        'container_count' => 0,
                        'available_cartons' => 0,
                        'loose_quantity' => 0.0,
                    ];
                }

                $productsSummary[$productId]['received_quantity'] += (float) $item->quantity;
                $totalReceived += (float) $item->quantity;
            }

            $containers[] = $container;
        }

        $saleItems = SaleItem::query()
            ->select('sale_items.*', 'sales.sale_date as sale_date')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereNull('sale_items.deleted_at')
            ->whereNull('sales.deleted_at')
            ->when($departmentId !== '', function ($query) use ($departmentId) {
                $query->where('sales.department_id', $departmentId);
            })
            ->orderBy('sales.sale_date')
            ->orderBy('sale_items.id')
            ->get();

        foreach ($saleItems as $saleItem) {
            $productId = (string) $saleItem->product_id;
            $eventsByProduct[$productId][] = [
                'type' => 'sale',
                'date' => $saleItem->sale_date,
                'order' => $saleItem->id,
                'quantity' => (float) $saleItem->quantity,
                'sale_item' => $saleItem,
            ];

            if (!isset($productsSummary[$productId])) {
                $productsSummary[$productId] = [
                    'product_id' => (string) $saleItem->product_id,
                    'product_name' => $saleItem->product?->name ?? 'Product',
                    'received_quantity' => 0.0,
                    'sold_quantity' => 0.0,
                    'remaining_quantity' => 0.0,
                    'container_count' => 0,
                    'available_cartons' => 0,
                    'loose_quantity' => 0.0,
                ];
            }

            $productsSummary[$productId]['sold_quantity'] += (float) $saleItem->quantity;
            $totalSold += (float) $saleItem->quantity;
        }

        $returnItems = ProductReturnList::query()
            ->select('product_return_lists.*', 'product_returns.date as return_date', 'product_returns.department_id as return_department_id')
            ->join('product_returns', 'product_returns.id', '=', 'product_return_lists.return_id')
            ->whereNull('product_return_lists.deleted_at')
            ->whereNull('product_returns.deleted_at')
            ->when($departmentId !== '', function ($query) use ($departmentId) {
                $query->where('product_returns.department_id', $departmentId);
            })
            ->orderBy('product_returns.date')
            ->orderBy('product_return_lists.id')
            ->get();

        foreach ($returnItems as $returnItem) {
            $productId = (string) $returnItem->product_id;
            $batch = (object) [
                'source_type' => 'return',
                'source_id' => $returnItem->id,
                'return_id' => $returnItem->return_id,
                'product_id' => $returnItem->product_id,
                'reference' => null,
                'product_name' => $returnItem->product?->name ?? 'Product',
                'received_quantity' => (float) $returnItem->quantity,
                'sold_quantity' => 0.0,
                'remaining_quantity' => (float) $returnItem->quantity,
                'available_cartons' => 0,
                'loose_quantity' => 0.0,
                'carton_size' => $this->resolvedUnitsPerPackage($returnItem->product),
                'unit_price' => (float) $returnItem->price,
                'date' => $returnItem->return_date,
            ];

            $returnAvailability = $this->packagingAvailability(
                $batch->remaining_quantity,
                $returnItem->product
            );
            $batch->available_cartons = $returnAvailability['available_cartons'];
            $batch->loose_quantity = $returnAvailability['loose_quantity'];

            $eventsByProduct[$productId][] = [
                'type' => 'supply',
                'date' => $returnItem->return_date,
                'order' => $returnItem->id,
                'batch' => $batch,
            ];

            if (!isset($productsSummary[$productId])) {
                $productsSummary[$productId] = [
                    'product_id' => (string) $returnItem->product_id,
                    'product_name' => $returnItem->product?->name ?? 'Product',
                    'received_quantity' => 0.0,
                    'sold_quantity' => 0.0,
                    'remaining_quantity' => 0.0,
                    'container_count' => 0,
                    'available_cartons' => 0,
                    'loose_quantity' => 0.0,
                ];
            }

            $productsSummary[$productId]['received_quantity'] += (float) $returnItem->quantity;
            $totalReceived += (float) $returnItem->quantity;
        }

        foreach ($eventsByProduct as $productId => $events) {
            usort($events, function (array $a, array $b) {
                $dateComparison = strcmp((string) $a['date'], (string) $b['date']);
                if ($dateComparison !== 0) {
                    return $dateComparison;
                }

                $priorityA = $a['type'] === 'sale' ? 1 : 0;
                $priorityB = $b['type'] === 'sale' ? 1 : 0;
                if ($priorityA !== $priorityB) {
                    return $priorityA <=> $priorityB;
                }

                return $a['order'] <=> $b['order'];
            });

            $queue = [];
            foreach ($events as $event) {
                if ($event['type'] !== 'sale') {
                    $queue[] = $event['batch'];
                    continue;
                }

                $saleItem = $event['sale_item'];
                $this->applySaleConsumption($queue, $saleItem);
            }
        }

        foreach ($containers as $container) {
            foreach ($container->items as $item) {
                $availability = $this->packagingAvailabilityFromUnits(
                    (float) $item->remaining_quantity,
                    (int) ($item->carton_size ?? 0)
                );
                $item->available_cartons = $availability['available_cartons'];
                $item->loose_quantity = $availability['loose_quantity'];
            }
        }

        foreach ($containers as $container) {
            foreach ($container->items as $item) {
                $container->sold_total += $item->sold_quantity;
                $container->remaining_total += $item->remaining_quantity;
            }
        }

        foreach ($containers as $container) {
            foreach ($container->items as $item) {
                $productId = (string) $item->product_id;
                if (isset($productsSummary[$productId])) {
                    $productsSummary[$productId]['remaining_quantity'] += $item->remaining_quantity;
                    $productsSummary[$productId]['container_count'] += 1;
                    $productsSummary[$productId]['available_cartons'] += (int) ($item->available_cartons ?? 0);
                    $productsSummary[$productId]['loose_quantity'] += (float) ($item->loose_quantity ?? 0.0);
                }
            }
        }

        $stockQuantities = ProductStock::query()
            ->whereIn('product_id', array_keys($productsSummary))
            ->pluck('quantity', 'product_id');

        foreach ($productsSummary as $productId => &$summary) {
            $summary['catalog_quantity'] = (float) ($stockQuantities[$productId] ?? 0);
            $summary['fifo_remaining_quantity'] = (float) $summary['remaining_quantity'];
            $summary['remaining_quantity'] = (float) $summary['fifo_remaining_quantity'];
        }
        unset($summary);

        $totalRemaining = array_reduce(
            $productsSummary,
            fn (float $carry, array $item) => $carry + (float) ($item['fifo_remaining_quantity'] ?? 0),
            0.0
        );
        $totalAvailableCartons = array_reduce(
            $productsSummary,
            fn (int $carry, array $item) => $carry + (int) ($item['available_cartons'] ?? 0),
            0
        );
        $totalLooseQuantity = array_reduce(
            $productsSummary,
            fn (float $carry, array $item) => $carry + (float) ($item['loose_quantity'] ?? 0),
            0.0
        );

        return response()->json([
            'success' => true,
            'summary' => [
                'container_count' => count($containers),
                'product_count' => count($productsSummary),
                'received_quantity' => $totalReceived,
                'sold_quantity' => $totalSold,
                'remaining_quantity' => $totalRemaining,
                'available_cartons' => $totalAvailableCartons,
                'loose_quantity' => $totalLooseQuantity,
            ],
            'containers' => $containers,
            'products' => array_values($productsSummary),
        ]);
    }

    /**
     * Store a new supply (buying invoice).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->input('data');
        
        try {
            DB::beginTransaction();
            $referenceSequenceByKey = [];

            $supply = Supply::create([
                'supply_date' => $data['supply_date'],
                'sales_supplier_id' => $data['supplier']['id'],
                'departement_id' => $data['departement_id'],
                'total_amount' => $data['total_amount'],
                'notes' => $data['notes'] ?? null,
            ]);

            if (isset($data['supply_items'])) {
                foreach ($data['supply_items'] as $item) {
                    $reference = $this->resolveSupplyItemReference(
                        $item,
                        (int) $item['product']['id'],
                        isset($data['departement_id']) ? (int) $data['departement_id'] : null,
                        $referenceSequenceByKey
                    );

                    SupplyItem::create([
                        'supply_id' => $supply->id,
                        'product_id' => $item['product']['id'],
                        'sales_supplier_id' => $data['supplier']['id'],
                        'reference' => $reference,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['quantity'] * $item['unit_price'],
                        'supply_date' => $data['supply_date'],
                    ]);

                    $this->adjustProductStock(
                        (int) $item['product']['id'],
                        (float) $item['quantity'],
                        'supply_received',
                        'supply_item',
                        null,
                        isset($data['departement_id']) ? (int) $data['departement_id'] : null,
                        'Stock increased from supply import.',
                        [
                            'supply_id' => $supply->id,
                            'reference' => $reference,
                        ]
                    );
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Supply created successfully', "id" => $supply->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Update an existing supply.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->input('data');
        
        try {
            DB::beginTransaction();
            $referenceSequenceByKey = [];

            $supply = Supply::findOrFail($id);
            $supply->update([
                'supply_date' => $data['supply_date'],
                'sales_supplier_id' => $data['supplier']['id'],
                'departement_id' => $data['departement_id'],
                'total_amount' => $data['total_amount'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Reverse old stock and clear items
            $oldItems = SupplyItem::where('supply_id', $supply->id)->get();
            foreach ($oldItems as $oldItem) {
                $this->adjustProductStock(
                    (int) $oldItem->product_id,
                    -(float) $oldItem->quantity,
                    'supply_reversed',
                    'supply_item',
                    (int) $oldItem->id,
                    (int) $supply->departement_id,
                    'Stock reversed because the supply was updated.',
                    [
                        'supply_id' => $supply->id,
                        'reference' => $oldItem->reference,
                    ]
                );
            }
            SupplyItem::where('supply_id', $supply->id)->delete();

            if (isset($data['supply_items'])) {
                foreach ($data['supply_items'] as $item) {
                    $reference = $this->resolveSupplyItemReference(
                        $item,
                        (int) $item['product']['id'],
                        isset($data['departement_id']) ? (int) $data['departement_id'] : null,
                        $referenceSequenceByKey
                    );

                    SupplyItem::create([
                        'supply_id' => $supply->id,
                        'product_id' => $item['product']['id'],
                        'sales_supplier_id' => $data['supplier']['id'],
                        'reference' => $reference,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['quantity'] * $item['unit_price'],
                        'supply_date' => $data['supply_date'],
                    ]);

                    $this->adjustProductStock(
                        (int) $item['product']['id'],
                        (float) $item['quantity'],
                        'supply_received',
                        'supply_item',
                        null,
                        isset($data['departement_id']) ? (int) $data['departement_id'] : (int) $supply->departement_id,
                        'Stock increased from supply update.',
                        [
                            'supply_id' => $supply->id,
                            'reference' => $reference,
                        ]
                    );
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Supply updated successfully', "id" => $supply->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display a specific supply with its items.
     */
    public function show($id): JsonResponse
    {
        $supply = Supply::with(['supplier', 'department', 'items.product'])->findOrFail($id);
        return response()->json(["supply" => $supply]);
    }

    /**
     * Delete a supply and its items.
     */
    public function delete(Request $request): JsonResponse
    {
        $id = $request->input('id');
        try {
            DB::beginTransaction();
            $supply = Supply::findOrFail($id);
            $items = SupplyItem::where('supply_id', $id)->get();
            foreach ($items as $item) {
                $this->adjustProductStock(
                    (int) $item->product_id,
                    -(float) $item->quantity,
                    'supply_deleted',
                    'supply_item',
                    (int) $item->id,
                    (int) $supply->departement_id,
                    'Stock decreased because the supply was deleted.',
                    [
                        'supply_id' => $supply->id,
                        'reference' => $item->reference,
                    ]
                );
            }

            SupplyItem::where('supply_id', $id)->delete();
            $supply->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 400);
        }

        return response()->json(['success' => true, 'message' => 'Supply deleted successfully']);
    }
}
