<?php

namespace App\Http\Controllers;

use App\Http\Helpers\NumberToLetter;
use App\Models\CertifyInvoiceProducts;
use App\Models\CertifyInvoices;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CertifyInvoiceController extends Controller
{

    /**
     * get List Of All Invoices
     *
     * @param Request $request
     * @return JsonResponse
     */

    #[OA\Get(
        path: "/api/certifyInvoices/list",
        operationId: "getInvoices",
        description: "Returns the list of invoices",
        tags: ["certifyInvoice"],
    )]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/ICertifyInvoice",
        type: 'object'
    )])]
    public function getInvoices(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided


        $invoices = CertifyInvoices::paginate($perPage, ['*'], 'page', $currentPage);
        $totalInvoices = $invoices->total(); // Total number of invoices matching the query
        $totalPage = ceil($totalInvoices / $perPage); // Calculate total pages

        return response()->json(["invoices" => $invoices, "totalPage" => $totalPage, "totalInvoices"=>$totalInvoices]);
    }

    public function getInvoice($id): JsonResponse
    {
        $invoice = CertifyInvoices::find($id);
        $invoice->amount_letter = $this->convertAmoutToLetter(($invoice->amount*1.19));

        $clients = Client::all();
        $products = Product::all();
        $selectedProductIds = $invoice->certifyInvoiceProducts->pluck('product_id')->toArray();

        $unSelectedProducts = Product::whereNotIn('id',$selectedProductIds)->get();

        return response()->json(["invoice" => $invoice, "clients"=>$clients, "products"=>$products, "unSelectedProducts"=>$unSelectedProducts]);
    }

    /**
     * Create a new Certify Invoice
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/certifyInvoices/store",
        operationId: "store",
        description: "Create a new Certify Invoice",
        tags: ["certifyInvoice"],
    )]
    #[OA\RequestBody(
        required: true,
        content: [
            new OA\JsonContent(
                required: ["client_id", "amount", "date"],
                properties: [
                    new OA\Property(property: "client_id", type: "integer", example: "1"),
                    new OA\Property(property: "amount", type: "number", format: "float", example: "123"),
                    new OA\Property(property: "date",  type: "string", format: "date-time", example: "2023-08-13"),
                    new OA\Property(property: "payment_type", type: "integer", example: "1"),
                    new OA\Property(property: "products", type: "array",
                        items: new OA\Items(
                            properties: [  // Example object of type object
                            new OA\Property(property: "product_id", type: "integer", example: "1"),
                            new OA\Property(property: "quantity", type: "number", example: "2"),
                            new OA\Property(property: "price", type: "number", example: "2"),
                            new OA\Property(property: "total", type: "number", example: "2"),
                            ],

                        )),

                ]
            )
        ]
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: [
            new OA\JsonContent(
                ref: "#/components/schemas/ICertifyInvoice",
                type: 'object'
            )
        ]
    )]

    public function store(Request $request): JsonResponse
    {
        $invoiceData = $request->input('invoiceData');
        $invoice = $invoiceData['invoice'];
        $client = $invoice['client'];

        $fac_id = $invoice['id'];

         $invoice = CertifyInvoices::create([
            'fac_id' => $fac_id,
            'date' => $invoice['date'],
            'client_id' => $client['id'],
            'amount' => $invoice['total'],
            'payment_type' => $invoiceData['selectedPaymentMethod'],
        ]);

        $products = $invoiceData['purchasedProducts'];

        foreach ($products as $product) {

            CertifyInvoiceProducts::create([
               'product_id' => $product['_value']['id'],
               'price' => $product['_value']['price'],
               'quantity' => $product['_value']['product_stock']['quantity'],
               'total' => $product['_value']['product_stock']['quantity'] * $product['_value']['price'],
               'certify_invoice_id' => $invoice->id,
            ]);
        }

        return response()->json(['message' => 'Product created successfully', "id"=>$invoice->id]);

    }

    /**
     * get List Of Clients and Products
     *
     * @param Request $request
     * @return JsonResponse
     */

    #[OA\Get(
        path: "/api/certifyInvoices/getData",
        operationId: "getData",
        description: "Returns the list of clients and products",
        tags: ["certifyInvoice"],
    )]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        type: 'object'
    )])]
    public function getData(Request $request): JsonResponse {
        $clients = Client::all();
        $products = Product::all();
        $date = date('Y-m-d');
        $id = $this->getLastIDPerYear(date('Y-m-d'));

        return response()->json(["clients" => $clients, "products" => $products, "id"=>$id, "date"=>$date]);
    }

    public function getLastID(Request $request){
        $date = $request->input('date');

        $last_id = $this->getLastIDPerYear($date);

        return response()->json(["id" => $last_id]);
    }

    public function getLastIDPerYear($date){

        $year = date("Y",strtotime($date));

        $last_id = CertifyInvoices::whereYear('date',$year)->orderBy('fac_id')->get()->last();

        if($last_id == null)
            $last_id = 1;
        else
            $last_id = $last_id->fac_id+1;

        return $last_id;
    }

    public function convertAmoutToLetter($amount){
        $lettre = new NumberToLetter();
        $amountLetter = "";
        if($this->isDecimal($amount)){
            list($int, $float) = explode('.',  $amount);
            $amountLetter =  $lettre->Conversion($int)." Dinar(s)";
            $centime = "";
            if($float>0){
                $centime =  $lettre->Conversion($float)." Centimes";
            }
            $amountLetter = $amountLetter.' et '.$centime;
        }else{
            $amountLetter =  $lettre->Conversion($amount)."Dinar(s)";

        }

        return $amountLetter;
    }

    function isDecimal( $val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }

}
