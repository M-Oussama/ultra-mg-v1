<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use App\Models\City;
use App\Models\Client;
use App\Models\Company;
use App\Models\PartialPayment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleStatus;
use App\Models\TruckDriver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class POSController extends Controller
{
    //
    public function getSales(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided
        $client_id = $request->input('client_id',  ''); // Default current page value is 1 if not provided
        $status = $request->input('status',  '');
        $from = $request->input('from',  '');
        $to = $request->input('to',  '');


        $sales = Sale::query();


        if($client_id !='') {
            $sales->where('client_id', $client_id);
        }
        if(intval($status) == 1) {
            $sales->where('balance', 0);
        }
        if(intval($status) == 2) {
            $sales->where('balance', '>',0);
        }

        if($from != '' && $to!='') {
            $sales->whereBetween('sale_date', [$from, $to]);
        }else {
            if($from != ''){
                $sales->whereBetween('sale_date', [$from, date('Y-m-d')]);
            }
        }
        $sales->orderBy('sale_date', 'desc');

        $paginatedResult = $sales->paginate($perPage, ['*'], 'page', $currentPage);

        $items = $paginatedResult->items();
        $totalSales = $paginatedResult->total(); // Total number of invoices matching the query
        $totalPage = ceil($totalSales / $perPage); // Calculate total pages

        $clients = Client::all();

        return response()->json(["sales" => $items, "totalPage" => $totalPage, "totalSales"=>$totalSales, 'clients' => $clients]);
    }

    public function getData() {
        $clients = Client::all();
        $drivers = TruckDriver::all();
        $products = Product::getAllProductsFormatted();
        $sale_statues = SaleStatus::all();
        $last_id = Sale::latest()->first();
        if(!$last_id) {
            $last_id = 0;
        } else {
            $last_id = $last_id->id;
        }


        $cities = City::all();

        return response()->json(["clients" => $clients, 'drivers'=>$drivers, 'cities'=>$cities, "products" => $products, "sale_statues"=>$sale_statues,"last_id"=>$last_id+1]);

    }

    public function getSale($saleId) {

        $sale = Sale::find($saleId);
        $payment_total =  Payment::where('sale_id', $saleId)
            ->sum('amount_paid');
        $sale->payment_total = $payment_total;
        $sale->amount_letter = $this->convertAmoutToLetter(($sale->total_amount*1.19));

        $companies = Company::all();

        $clients = Client::all();

        $sold = Sale::where('client_id',$sale->client_id)
            ->where('id', '!=', $sale->id)
            ->where('sale_date','<=', $sale->sale_date)
            ->sum('balance');

        return response()->json(["sold"=>$sold, "sale" => $sale, "companies" => $companies, "clients"=>$clients]);
    }

    public function getSaleData($saleId) {

        $sale = Sale::find($saleId);

        $sale->amount_letter = $this->convertAmoutToLetter(($sale->total_amount*1.19));

        $payment_total =  Payment::where('sale_id', $saleId)
            ->sum('amount_paid');
        $sale->payment_total = $payment_total;
        $sale->paymentAmount = $payment_total;
        //$sale->payment = false;

        $clients = Client::all();
        $drivers = TruckDriver::all();
        $products = Product::getAllProductsFormatted();
        $sale_statues = SaleStatus::all();

        $cities = City::all();
        return response()->json(["sale" => $sale, "cities" => $cities,"clients"=>$clients,"drivers"=>$drivers, "products"=> $products, "sale_statues"=>$sale_statues]);
    }

    public function getPriceHistory($productId, $clientId) {

        $products = SaleItem::where('client_id',$clientId)->where('product_id',$productId)->get();

        return response()->json(['products'=> $products]);
    }

    public function store(Request $request): JsonResponse
    {

        $data = $request->input('data');
        $client = $data['client'];
        $payment = $data['payment'];
        $balance =  $data['total_amount'] - $data['paymentAmount'];

        if($payment) {
            if($balance >= 0) {
                $sale_status = SaleStatus::PAID_ID;
            }else {
                $sale_status = SaleStatus::PARTIALLY_PAID_ID;
            }
        }else {
            $sale_status = SaleStatus::NOT_PAID_ID;
        }

        if($sale_status != SaleStatus::NOT_PAID_ID ) {
            $sale = Sale::create([
                'sale_date' => $data['sale_date'],
                'client_id' => $client['id'],
                'total_amount' => $data['total_amount'],
                'sale_statuses_id' => $sale_status,
                'balance' => $balance,
                'regulation' => $data['paymentAmount'],
                'payment' => 1
            ]);
        } else {
            $sale = Sale::create([
                'sale_date' => $data['sale_date'],
                'client_id' => $client['id'],
                'total_amount' => $data['total_amount'],
                'sale_statuses_id' => $sale_status,
                'balance' => $balance,
            ]);

        }
        if($data['driver_id']){
            $sale->truck_driver_id = $data['driver_id'];
            $sale->picked_up = 0;
            $sale->save();
        }
        if(floatval($data['paymentAmount']) > 0) {
            $payment = new Payment();
            $payment->payment_date = $data['sale_date'];
            $payment->amount_paid = $data['paymentAmount'];
            $payment->sale_id = $sale->id;
            $payment->client_id = $client['id'];
            $payment->active = true;
            $payment->save();
        }



        $products = $data['sale_items'];

        foreach ($products as $product) {
          $object=  SaleItem::create([
                'product_id' => $product['product']['id'],
                'client_id' => $client['id'],
                'quantity' => $product['quantity'],
                'total_price' => $product['quantity'] * $product['price'],
                'sale_id' => $sale->id,
                'sale_date' => $data['sale_date'],
            ]);
            $object->price = floatval($product['price']);
            $object->save();
        }

//        $benefit = Benefit::where('month', date("m", strtotime($data['sale_date'])))->where('year', date("Y", strtotime($data['sale_date'])))->get();
//        if(count($benefit) > 0) {
//            $benefitController = new BenefitController();
//            $benefitController->refreshBenefitColumns($benefit->first()->id);
//        }

        return response()->json(['message' => 'Product created successfully', "id"=>$sale->id]);
    }

    public function update(Request $request): JsonResponse
    {

        $data = $request->input('data');

        $client = $data['client'];


        $payment = $data['payment'];
        $balance =  $data['total_amount'] - $data['paymentAmount'];

        if($balance >= 0) {
            $sale_status = SaleStatus::PAID_ID;
        }else {
             $sale_status = SaleStatus::NOT_PAID_ID;
        }


        $sale = Sale::find($data['id']);

        if($sale_status != SaleStatus::NOT_PAID_ID && $payment == 1) {

            $sale->update([
                'sale_date' => $data['sale_date'],
                'client_id' => $client['id'],
                'total_amount' => $data['total_amount'],
                'sale_statuses_id' => $sale_status,
                'balance' => $balance - floatval($data['regulation']),
            ]);
            $sale->payment = 1;
            $sale->regulation = floatval($data['regulation']);
            $sale->save();


        } else{

            $sale->update([
                'sale_date' => $data['sale_date'],
                'client_id' => $client['id'],
                'total_amount' => $data['total_amount'],
                'sale_statuses_id' => $sale_status,
                'balance' => $balance,

            ]);
            $sale->payment = 0;
            $sale->regulation = 0;
            $sale->save();
        }

        if($data['truck_driver_id']){
            $sale->truck_driver_id = $data['truck_driver_id'];
            $sale->save();
        }

        if(floatval($data['regulation']) > 0) {

            $payment = Payment::where('sale_id', $sale->id)->where('active', true)->get()->first();

            if(!$payment){
                $payment = new Payment();
            }
            $payment->payment_date = $data['sale_date'];
            $payment->amount_paid = floatval($data['regulation']);
            $payment->sale_id = $sale->id;
            $payment->client_id = $client['id'];
            $payment->active = true;
            $payment->save();
        }

        $saleItems = SaleItem::where('sale_id',$sale->id)->get();

        foreach ($saleItems as $item) {
            $item->delete();
        }

        $products = $data['sale_items'];

        foreach ($products as $product) {
            $object=  SaleItem::create([
                'product_id' => $product['product']['id'],
                'quantity' => $product['quantity'],
                'total_price' => $product['quantity'] * $product['price'],
                'sale_id' => $sale->id,
                'client_id' => $client['id'],
                'sale_date' => $data['sale_date'],
            ]);
            $object->price = floatval($product['price']);
            $object->save();
        }

//        $benefit = Benefit::where('month', date("m", strtotime($data['sale_date'])))->where('year', date("Y", strtotime($data['sale_date'])))->get();
//        if(count($benefit) > 0) {
//            $benefitController = new BenefitController();
//            $benefitController->refreshBenefitColumns($benefit->first()->id);
//        }

        return response()->json(['message' => 'Product updated successfully', "id"=>$sale->id]);
    }

    public function addPayment(Request $request) {

        $payment = $request->input('payment');

        $date = $payment['date'];
        $amount = $payment['amount'];
        $note = $payment['note'];
        $sale = $payment['sale'];

        $payment = new Payment();
        $payment->payment_date = $date;
        $payment->amount_paid = $amount;
        $payment->note = $note;
        $payment->sale_id = $sale['id'];
        $payment->client_id = $sale['client']['id'];
        $payment->save();

        $_sale = Sale::find($sale['id']);
        $_sale->balance = $_sale->balance - $amount;
        $_sale->save();

        $client = Client::find($sale['client']['id']);
        $this->calculateClientBalance($client);

        return response()->json('Payment Added Successfully');
    }

    public function createPayment(Request $request) {


        $payment = $request->input('payment');
        $paidInvoices = $request->input('paidInvoices');

        $date = $payment['date'];
        $amount = $payment['amount'];
        $note = $payment['note'];
        $client_id = $payment['client']['id'];


        $payment = new Payment();
        $payment->payment_date = $date;
        $payment->amount_paid = $amount;
        $payment->note = $note;
        $payment->client_id = $client_id;
        $payment->save();

        foreach ($paidInvoices as $paidInvoice) {
            $invoice = Sale::find($paidInvoice['id']);

            $amount = (floatval($invoice->balance) - floatval($paidInvoice['balance']));

            $invoice->update(['balance' => $amount ]);
            PartialPayment::create([
               'payment_id' => $payment->id,
               'sale_id' => $invoice->id,
               'amount' => $paidInvoice['balance'],
            ]);

        }

        $client = Client::find($client_id);
        $this->calculateClientBalance($client);

        return response()->json('Payment Added Successfully');
    }

    public function updatePayment(Request $request) {

        $payment = $request->input('payment');
        $paidInvoices = $request->input('paidInvoices');


        $date = $payment['payment_date'];
        $amount = $payment['amount_paid'];
        $note = $payment['note'];
        $sale_id = $payment['sale_id'];
        $client_id = $payment['client_id'];
        $id = $payment['id'];

        $payment = Payment::find($id);
        $payment->payment_date = $date;
        $payment->amount_paid = $amount;
        $payment->note = $note;
        $payment->client_id = $client_id;
        $payment->sale_id = $sale_id;
        $payment->save();

        $partialPayments = PartialPayment::where('payment_id', $payment->id)->get();



        foreach ($partialPayments as $partialPayment) {
            $invoice = Sale::find($partialPayment->sale_id);

            $newBalance = floatval($invoice->balance) + floatval($partialPayment->amount);
            $invoice->update(['balance' => $newBalance]);
        }

        PartialPayment::where('payment_id', $payment->id)->delete();


        foreach ($paidInvoices as $paidInvoice) {
            $invoice = Sale::find($paidInvoice['sale_id']);

            $amount = (floatval($invoice->balance) - floatval($paidInvoice['balance']));

            $invoice->update(['balance' => $amount ]);
            PartialPayment::create([
                'payment_id' => $payment->id,
                'sale_id' => $invoice->id,
                'amount' => $paidInvoice['balance'],
            ]);

        }
        $client = Client::find($client_id);
        $this->calculateClientBalance($client);
//        if($payment->active) {
//            if($payment->sale_id){
//                $sale = Sale::find($payment->sale_id);
//                $sale->balance = floatval($sale->balance) - floatval($sale->regulation) +  floatval($amount);
//                $sale->regulation = $amount;
//                $sale->save();
//            }
//
//        }


        return response()->json('Payment updated Successfully');
    }
    public function deletePayment(Request $request) {

        $payment = $request->input('payment');

        $id = $payment['id'];
        $payment = Payment::find($id);
        if($payment->active){
            if($payment->sale_id){
                $sale = Sale::find($payment->sale_id);
                $sale->balance = $sale->balance + $sale->regulation;
                $sale->regulation = 0;
                $sale->save();
            }
        }
        $payment->delete();

        return response()->json('Payment deleted Successfully');
    }

    public function listPayment(Request $request){
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided


        $payments = Payment::orderBy('payment_date', 'desc')->paginate($perPage, ['*'], 'page', $currentPage);
        $totalSales = $payments->total(); // Total number of payments matching the query
        $totalPage = ceil($totalSales / $perPage); // Calculate total pages
        $clients = Client::all();
        return response()->json(["payments" => $payments, "totalPage" => $totalPage, "totalPayments"=>$totalSales,"clients"=>$clients]);

    }

    public function deleteSale(Request $request) {

        $id = $request->input('sale.id');
        $sale = Sale::find($id);

        SaleItem::where('sale_id', $id)->delete();
        Payment::where('sale_id', $id)->delete();

        $sale->delete();

        return response()->json('Sale deleted Successfully');

    }

    public function getClientInvoices($id){
        $notPaidInvoices = Sale::where('balance', '>', 0)->where('client_id', $id)->get();
        return response()->json(["notPaidInvoices" => $notPaidInvoices]);

    }
    public function getPaidInvoices($id, $payment_id){

        $notPaidInvoices = Sale::where('balance', '>', 0)->where('client_id', $id)->get();

        $paidInvoices = PartialPayment::where('payment_id', $payment_id)->get();

        return response()->json(["notPaidInvoices" => $notPaidInvoices, "paidInvoices" => $paidInvoices]);


    }

    public function updatePickUp(Request $request, Sale $sale)
    {

        // Validate that we got a boolean from frontend
        $validated = $request->validate([
            'picked_up' => 'required|boolean',
        ]);

        $sale->picked_up = $validated['picked_up'];
        $sale->save();

        return response()->json([
            'message' => 'Sale pickup status updated.',
            'sale' => $sale
        ]);
    }

}
