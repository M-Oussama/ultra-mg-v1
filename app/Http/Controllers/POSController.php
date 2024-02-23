<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use App\Models\City;
use App\Models\Client;
use App\Models\Company;
use App\Models\Payment;
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


        $sales = Sale::orderBy('sale_date', 'desc')->paginate($perPage, ['*'], 'page', $currentPage);
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


        $cities = City::all();

        return response()->json(["clients" => $clients, 'cities'=>$cities, "products" => $products, "sale_statues"=>$sale_statues,"last_id"=>$last_id+1]);

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
            ->where('balance','>',0)
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
        $products = Product::getAllProductsFormatted();
        $sale_statues = SaleStatus::all();

        $cities = City::all();
        return response()->json(["sale" => $sale, "cities" => $cities,"clients"=>$clients, "products"=> $products, "sale_statues"=>$sale_statues]);
    }

    public function getPriceHistory($clientId, $productId) {
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

        return response()->json('Payment Added Successfully');
    }

    public function createPayment(Request $request) {

        $payment = $request->input('payment');

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


        return response()->json('Payment Added Successfully');
    }

    public function updatePayment(Request $request) {

        $payment = $request->input('payment');

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


        return response()->json('Payment updated Successfully');
    }
    public function deletePayment(Request $request) {

        $payment = $request->input('payment');

        $id = $payment['id'];
        $payment = Payment::find($id);
        $payment->delete();

        return response()->json('Payment deleted Successfully');
    }

    public function listPayment(Request $request){
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided


        $payments = Payment::paginate($perPage, ['*'], 'page', $currentPage);
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

}
