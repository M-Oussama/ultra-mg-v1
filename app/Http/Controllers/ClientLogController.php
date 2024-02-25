<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientLogController extends Controller
{
    //

    public function getLog(Request $request){

        $client_id = $request->input('params.client_id');
        $from_date = $request->input('params.from_date');
        $to_date = $request->input('params.to_date');
        $client = Client::find($client_id);
        $searchValue = $request->input('params.searchValue', ''); // search value
        $perPage = $request->input('params.perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('params.currentPage', 1); // Default current page value is 1 if not provided



       $sale =  Sale::select(
           'id',
           'client_id',
           'sale_date as date',
           'total_amount as amount',
           DB::raw("'sale' as type"),
           DB::raw("0 as counted"),
           DB::raw("0 as total_balance"),
           'balance',
           'regulation'
       )->whereBetween('sale_date', [$from_date, $to_date]);
       $payments = Payment::select(
           'id',
           'client_id',
           'payment_date as date',
           'amount_paid as amount',
           DB::raw("'payment' as type"),
           DB::raw("0 as counted"),
           DB::raw("0 as total_balance"),
           DB::raw("0 as balance"),
           DB::raw("0 as  regulation"),

       )->whereBetween('payment_date', [$from_date, $to_date])->union($sale)->orderBy('date')->paginate($perPage, ['*'], 'page', $currentPage);;

        $totalLogs = $payments->total(); // Total number of users matching the query
        $totalPage = ceil($totalLogs / $perPage); // Calculate total pages

        return response()->json([
            'logs'=> $payments,
            'client' =>$client,
            "totalPage" => $totalPage,
            "totalLogs"=>$totalLogs
        ]);
    }
}
