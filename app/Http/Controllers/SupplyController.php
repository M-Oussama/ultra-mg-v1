<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Department;
use App\Models\Product;
use App\Models\SalesSupplier;
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
