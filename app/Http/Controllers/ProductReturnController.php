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



        $return = ProductReturn::create([
                'date' => $data['sale_date'],
                'client_id' => $client['id'],
                'total_amount' => $data['total_amount'],
                 'paid' => $data['payment']
            ]);


       $products = $data['sale_items'];

        foreach ($products as $product) {
            $object=  ProductReturnList::create([
                'product_id' => $product['product']['id'],
                'client_id' => $client['id'],
                'quantity' => $product['quantity'],
                'total_price' => $product['quantity'] * $product['price'],
                'return_id' => $return->id,
                'price' => $product['price'],
                'date' => $data['sale_date'],
            ]);



        }

        return response()->json(['message' => 'Product Return added successfully', "id"=>$return->id]);
    }

    public function deleteReturn(Request $request) {

        $id = $request->input('sale.id');
        $return = ProductReturn::find($id);

        ProductReturnList::where('return_id', $id)->delete();

        $return->delete();

        return response()->json('Return deleted Successfully');

    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->input('data');
        $client = $data['client'];


        $return = ProductReturn::find($data['id']);



        $return->update([
                'date' => $data['sale_date'],
                'client_id' => $client['id'],
                'total_amount' => $data['total_amount'],
                'paid' => $data['paid']
            ]);
        $items = $return->ProductReturnList;

        foreach ($items as $item) {
            $item->delete();
        }
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

        }

        return response()->json(['message' => 'Return Products updated successfully', "id"=>$return->id]);
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
}
