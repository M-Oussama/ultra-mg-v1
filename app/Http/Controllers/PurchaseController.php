<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\SaleStatus;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function list(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided
        $supplier_id = $request->input('supplier_id',  ''); // Default current page value is 1 if not provided
        $status = $request->input('status',  '');
        $from = $request->input('from',  '');
        $to = $request->input('to',  '');


        $purchases = Purchase::query();


        if($supplier_id !='') {
            $purchases->where('supplier_id', $supplier_id);
        }
        if(intval($status) == 1) {
            $purchases->where('balance', 0);
        }
        if(intval($status) == 2) {
            $purchases->where('balance', '>',0);
        }

        if($from != '' && $to!='') {
            $purchases->whereBetween('date', [$from, $to]);
        }else {
            if($from != ''){
                $purchases->whereBetween('date', [$from, date('Y-m-d')]);
            }
        }
        $purchases->orderBy('date', 'desc');

        $paginatedResult = $purchases->paginate($perPage, ['*'], 'page', $currentPage);

        $items = $paginatedResult->items();
        $totalPurchases = $paginatedResult->total(); // Total number of invoices matching the query
        $totalPage = ceil($totalPurchases / $perPage); // Calculate total pages

        $suppliers = Supplier::all();

        return response()->json(["purchases" => $items, "totalPage" => $totalPage, "totalPurchases"=>$totalPurchases, 'suppliers' => $suppliers]);
    }

    public function getData() {
        $suppliers = Supplier::all();
        $products = Product::getAllProductsFormatted();
        $purchase_statues = SaleStatus::all();
        $last_id = Purchase::latest()->first();
        if(!$last_id) {
            $last_id = 0;
        } else {
            $last_id = $last_id->id;
        }


        $cities = City::all();

        return response()->json(["suppliers" => $suppliers, 'cities'=>$cities, "products" => $products, "purchase_statues"=>$purchase_statues,"last_id"=>$last_id+1]);

    }
}
