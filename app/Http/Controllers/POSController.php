<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class POSController extends Controller
{
    //
    public function getSales(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided


        $sales = Sale::paginate($perPage, ['*'], 'page', $currentPage);
        $totalSales = $sales->total(); // Total number of invoices matching the query
        $totalPage = ceil($totalSales / $perPage); // Calculate total pages

        return response()->json(["sales" => $sales, "totalPage" => $totalPage, "totalSales"=>$totalSales]);
    }

    public function getData() {
        $clients = Client::all();
        $products = Product::getAllProductsFormatted();
        $sale_statues = SaleStatus::all();
        $last_id = Sale::latest()->first();
        if(!$last_id) {
            $last_id = 0;
        } else {
            $last_id = $last_id->id;
        }


        return response()->json(["clients" => $clients, "products" => $products, "sale_statues"=>$sale_statues,"last_id"=>$last_id+1]);

    }

    public function store(Request $request): JsonResponse
    {

        $data = $request->input('data');
        $client = $data['client'];
        $sale_status = $data['sale_status'];

        $sale = Sale::create([
            'sale_date' => $data['sale_date'],
            'client_id' => $client['id'],
            'total_amount' => $data['total_amount'],
            'sale_statuses_id' => $sale_status['id'],
            'balance' => $data['total_amount'],
        ]);

        $products = $data['sale_items'];

        foreach ($products as $product) {

            SaleItem::create([
                'product_id' => $product['product']['id'],
                'unit_price' => $product['price'],
                'quantity' => $product['quantity'],
                'total_price' => $product['quantity'] * $product['price'],
                'sale_id' => $sale->id,
            ]);
        }

        return response()->json(['message' => 'Product created successfully', "id"=>$sale->id]);

    }
}
