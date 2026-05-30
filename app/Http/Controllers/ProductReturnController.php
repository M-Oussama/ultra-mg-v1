<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Client;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductReturn;
use App\Models\ProductReturnList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductStock;

class ProductReturnController extends Controller
{
    public function getReturns(Request $request): JsonResponse
{
    $searchValue = $request->input('searchValue', ''); // search value
    $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
    $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided
    $client_id = $request->input('client_id', '');
    $from = $request->input('from',  '');
    $to = $request->input('to',  '');
    $status = $request->input('status',  '');

    $returns = ProductReturn::query();

    if($client_id !='') {
        $returns->where('client_id', $client_id);
    }

    if($from != '' && $to!='') {
        $returns->whereBetween('sale_date', [$from, $to]);
    }else {
        if($from != ''){
            $returns->whereBetween('sale_date', [$from, date('Y-m-d')]);
        }
    }

    if($status !=''){
        $returns->where('paid', $status);
    }

    $paginatedResult = $returns->orderBy('date','desc')->paginate($perPage, ['*'], 'page', $currentPage);
    $items = $paginatedResult->items();

    $totalReturns = $paginatedResult->total(); // Total number of invoices matching the query
    $totalPage = ceil($totalReturns / $perPage); // Calculate total pages

    $clients = Client::all();

    return response()->json(["returns" => $items, "clients" => $clients, "totalPage" => $totalPage, "totalReturns"=>$totalReturns]);
}

    public function getData() {
        $clients = Client::all();
        $products = Product::getAllProductsFormatted();
        $last_id = ProductReturn::latest()->first();
        if(!$last_id) {
            $last_id = 0;
        } else {
            $last_id = $last_id->id;
        }


        $cities = City::all();

        return response()->json(["clients" => $clients, 'cities'=>$cities, "products" => $products, "sale_statues"=>[],"last_id"=>$last_id+1]);

    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->input('data');
        $client = $data['client'];

        try {
            DB::beginTransaction();

            $return = ProductReturn::create([
                'date' => $data['sale_date'],
                'client_id' => $client['id'],
                'total_amount' => $data['total_amount'],
                'paid' => $data['payment'],
                'department_id' => $data['department_id'] ?? $request->input('department_id', 1),
            ]);

            $products = $data['sale_items'];

            foreach ($products as $product) {
                ProductReturnList::create([
                    'product_id' => $product['product']['id'],
                    'client_id' => $client['id'],
                    'quantity' => $product['quantity'],
                    'total_price' => $product['quantity'] * $product['price'],
                    'return_id' => $return->id,
                    'price' => $product['price'],
                    'date' => $data['sale_date'],
                ]);

                // Increment Stock
                $stock = ProductStock::firstOrCreate(
                    ['product_id' => $product['product']['id']],
                    ['quantity' => 0]
                );
                $stock->increment('quantity', $product['quantity']);
            }

            DB::commit();
            return response()->json(['message' => 'Product Return added successfully', "id" => $return->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error adding return: ' . $e->getMessage()], 400);
        }
    }

    public function deleteReturn(Request $request) {
        $id = $request->input('sale.id');
        
        try {
            DB::beginTransaction();
            $return = ProductReturn::findOrFail($id);
            $items = ProductReturnList::where('return_id', $id)->get();

            foreach ($items as $item) {
                $stock = ProductStock::where('product_id', $item->product_id)->first();
                if ($stock) {
                    $stock->decrement('quantity', $item->quantity);
                }
            }

            ProductReturnList::where('return_id', $id)->delete();
            $return->delete();

            DB::commit();
            return response()->json('Return deleted Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json('Error deleting return: ' . $e->getMessage(), 400);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->input('data');
        $client = $data['client'];

        try {
            DB::beginTransaction();

            $return = ProductReturn::findOrFail($data['id']);

            // Reverse old stock
            $oldItems = ProductReturnList::where('return_id', $return->id)->get();
            foreach ($oldItems as $oldItem) {
                $stock = ProductStock::where('product_id', $oldItem->product_id)->first();
                if ($stock) {
                    $stock->decrement('quantity', $oldItem->quantity);
                }
            }
            ProductReturnList::where('return_id', $return->id)->delete();

            $return->update([
                'date' => $data['sale_date'],
                'client_id' => $client['id'],
                'total_amount' => $data['total_amount'],
                'paid' => $data['paid'],
                'department_id' => $data['department_id'] ?? $request->input('department_id', $return->department_id),
            ]);

            $products = $data['sale_items'];

            foreach ($products as $product) {
                ProductReturnList::create([
                    'product_id' => $product['product']['id'],
                    'quantity' => $product['quantity'],
                    'total_price' => $product['quantity'] * $product['price'],
                    'return_id' => $return->id,
                    'client_id' => $client['id'],
                    'price' => $product['price'],
                    'date' => $data['sale_date'],
                ]);

                // Increment Stock
                $stock = ProductStock::firstOrCreate(
                    ['product_id' => $product['product']['id']],
                    ['quantity' => 0]
                );
                $stock->increment('quantity', $product['quantity']);
            }

            DB::commit();
            return response()->json(['message' => 'Return Products updated successfully', "id" => $return->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error updating return: ' . $e->getMessage()], 400);
        }
    }

    public function getReturnData($returnId) {

        $return = ProductReturn::find($returnId);


        $return->sale_items = $return->ProductReturnList;

        $clients = Client::all();
        $products = Product::getAllProductsFormatted();

        $cities = City::all();
        return response()->json(["product_return" => $return, "cities" => $cities,"clients"=>$clients, "products"=> $products, "sale_statues"=>[]]);
    }
    public function getReturn($saleId) {

        $product_return = ProductReturn::find($saleId);

        $product_return->sale_items = $product_return->ProductReturnList;

        $product_return->amount_letter = $this->convertAmoutToLetter(($product_return->total_amount*1.19));

        $clients = Client::all();

        $company = Company::all();


        return response()->json(["sold"=>0,"product_return"=>$product_return, "companies" => $company, "clients"=>$clients]);
    }

    /**
     * Import product_returns from CSV.
     *
     * CSV columns:
     * id, total_amount, client_id, date, created_at, updated_at, deleted_at, paid
     */
    public function importReturnsCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        $departmentId = (int) $request->input('department_id');

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);

        foreach (['id', 'total_amount', 'client_id', 'date', 'created_at', 'updated_at', 'deleted_at', 'paid'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $warnings = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $paidRaw = strtolower((string) ($rowData['paid'] ?? '0'));
            $paid = in_array($paidRaw, ['1', 'true', 'yes'], true) ? 1 : 0;
            $deletedRaw = trim((string) ($rowData['deleted_at'] ?? ''));

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'department_id' => $departmentId,
                'total_amount' => isset($rowData['total_amount']) ? (float) $rowData['total_amount'] : 0,
                'client_id' => isset($rowData['client_id']) ? (int) $rowData['client_id'] : null,
                'date' => $rowData['date'] ?? null,
                'created_at' => $rowData['created_at'] ?? now(),
                'updated_at' => $rowData['updated_at'] ?? now(),
                'deleted_at' => ($deletedRaw === '' || strtolower($deletedRaw) === 'null') ? null : $deletedRaw,
                'paid' => $paid,
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'department_id' => 'required|integer|exists:departments,id',
                'total_amount' => 'required|numeric|min:0',
                'client_id' => 'required|integer',
                'date' => 'required|date',
                'created_at' => 'nullable|date',
                'updated_at' => 'nullable|date',
                'deleted_at' => 'nullable|date',
                'paid' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            if (!empty($payload['id']) && DB::table('product_returns')->where('id', $payload['id'])->exists()) {
                $warnings[] = [
                    'row' => $rowNumber,
                    'warning' => 'product_return id exists, inserted with auto-generated id.',
                ];
                unset($payload['id']);
            }

            DB::table('product_returns')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ]);
    }

    /**
     * Import product_return_lists from CSV.
     *
     * CSV columns:
     * id, return_id, client_id, product_id, quantity, price, total_price, date
     */
    public function importReturnListsCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);

        foreach (['id', 'return_id', 'client_id', 'product_id', 'quantity', 'price', 'total_price', 'date'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $warnings = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'return_id' => isset($rowData['return_id']) ? (int) $rowData['return_id'] : null,
                'client_id' => isset($rowData['client_id']) ? (int) $rowData['client_id'] : null,
                'product_id' => isset($rowData['product_id']) ? (int) $rowData['product_id'] : null,
                'quantity' => isset($rowData['quantity']) ? (float) $rowData['quantity'] : 0,
                'price' => isset($rowData['price']) ? (float) $rowData['price'] : 0,
                'total_price' => isset($rowData['total_price']) ? (float) $rowData['total_price'] : 0,
                'date' => $rowData['date'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'return_id' => 'required|integer',
                'client_id' => 'required|integer',
                'product_id' => 'required|integer',
                'quantity' => 'required|numeric|min:0',
                'price' => 'required|numeric|min:0',
                'total_price' => 'required|numeric|min:0',
                'date' => 'required|date',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            if (!empty($payload['id']) && DB::table('product_return_lists')->where('id', $payload['id'])->exists()) {
                $warnings[] = [
                    'row' => $rowNumber,
                    'warning' => 'product_return_list id exists, inserted with auto-generated id.',
                ];
                unset($payload['id']);
            }

            DB::table('product_return_lists')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ]);
    }
}
