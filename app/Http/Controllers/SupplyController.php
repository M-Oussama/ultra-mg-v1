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
                    'product_name' => $item->product?->name ?? 'Product',
                    'received_quantity' => (float) $item->quantity,
                    'sold_quantity' => 0.0,
                    'remaining_quantity' => (float) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'date' => $supply->supply_date,
                ];

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
            ];

            if (!isset($productsSummary[$productId])) {
                $productsSummary[$productId] = [
                    'product_id' => (string) $saleItem->product_id,
                    'product_name' => $saleItem->product?->name ?? 'Product',
                    'received_quantity' => 0.0,
                    'sold_quantity' => 0.0,
                    'remaining_quantity' => 0.0,
                    'container_count' => 0,
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
                'product_name' => $returnItem->product?->name ?? 'Product',
                'received_quantity' => (float) $returnItem->quantity,
                'sold_quantity' => 0.0,
                'remaining_quantity' => (float) $returnItem->quantity,
                'unit_price' => (float) $returnItem->price,
                'date' => $returnItem->return_date,
            ];

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

                $remainingToAllocate = (float) $event['quantity'];
                foreach ($queue as $batch) {
                    if ($remainingToAllocate <= 0) {
                        break;
                    }

                    if ($batch->remaining_quantity <= 0) {
                        continue;
                    }

                    $consumed = min($batch->remaining_quantity, $remainingToAllocate);
                    $batch->remaining_quantity -= $consumed;
                    $batch->sold_quantity += $consumed;
                    $remainingToAllocate -= $consumed;
                }
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
                }
            }
        }

        $stockQuantities = ProductStock::query()
            ->whereIn('product_id', array_keys($productsSummary))
            ->pluck('quantity', 'product_id');

        foreach ($productsSummary as $productId => &$summary) {
            $summary['catalog_quantity'] = (float) ($stockQuantities[$productId] ?? 0);
            $summary['fifo_remaining_quantity'] = (float) $summary['remaining_quantity'];
            $summary['remaining_quantity'] = $summary['catalog_quantity'];
        }
        unset($summary);

        $totalRemaining = array_reduce(
            $productsSummary,
            fn (float $carry, array $item) => $carry + (float) ($item['catalog_quantity'] ?? 0),
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

            $supply = Supply::create([
                'supply_date' => $data['supply_date'],
                'sales_supplier_id' => $data['supplier']['id'],
                'departement_id' => $data['departement_id'],
                'total_amount' => $data['total_amount'],
                'notes' => $data['notes'] ?? null,
            ]);

            if (isset($data['supply_items'])) {
                foreach ($data['supply_items'] as $item) {
                    SupplyItem::create([
                        'supply_id' => $supply->id,
                        'product_id' => $item['product']['id'],
                        'sales_supplier_id' => $data['supplier']['id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['quantity'] * $item['unit_price'],
                        'supply_date' => $data['supply_date'],
                    ]);

                    // Update Product Stock
                    $productStock = ProductStock::firstOrCreate(
                        ['product_id' => $item['product']['id']],
                        ['quantity' => 0]
                    );
                    $productStock->increment('quantity', $item['quantity']);
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
                $stock = ProductStock::where('product_id', $oldItem->product_id)->first();
                if ($stock) {
                    $stock->decrement('quantity', $oldItem->quantity);
                }
            }
            SupplyItem::where('supply_id', $supply->id)->delete();

            if (isset($data['supply_items'])) {
                foreach ($data['supply_items'] as $item) {
                    SupplyItem::create([
                        'supply_id' => $supply->id,
                        'product_id' => $item['product']['id'],
                        'sales_supplier_id' => $data['supplier']['id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['quantity'] * $item['unit_price'],
                        'supply_date' => $data['supply_date'],
                    ]);

                    // Update Product Stock
                    $productStock = ProductStock::firstOrCreate(
                        ['product_id' => $item['product']['id']],
                        ['quantity' => 0]
                    );
                    $productStock->increment('quantity', $item['quantity']);
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
                $stock = ProductStock::where('product_id', $item->product_id)->first();
                if ($stock) {
                    $stock->decrement('quantity', $item->quantity);
                }
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
