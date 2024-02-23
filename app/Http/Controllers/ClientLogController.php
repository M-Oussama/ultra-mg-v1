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

        $client = Client::find($client_id);

        $customerLog = collect([]);


        $sales = DB::table('sales')
            ->select('id', 'client_id', 'sale_date as date', 'notes', 'total_amount as amount', 'regulation', 'balance', DB::raw("'sale' as type"), DB::raw("0 as counted"), DB::raw("0 as total_balance"))
            ->where('client_id', $client_id)
            ->orderBy('sale_date');
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
       );
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

       )->union($sale)->orderBy('date')->get();
        return response()->json([
            'logs'=> $payments
        ]);
    }
}
