<?php

namespace App\Http\Controllers;

use App\Http\Helpers\NumberToLetter;
use App\Models\CertifyInvoiceProducts;
use App\Models\CertifyInvoices;
use App\Models\CertifyClient;
use App\Models\Payment;
use App\Models\CertifyProduct;
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


        $invoices = CertifyInvoices::orderBy('date', 'desc')->paginate($perPage, ['*'], 'page', $currentPage);
        $totalInvoices = $invoices->total(); // Total number of invoices matching the query
        $totalPage = ceil($totalInvoices / $perPage); // Calculate total pages

        return response()->json(["invoices" => $invoices, "totalPage" => $totalPage, "totalInvoices"=>$totalInvoices]);
    }

    public function getInvoice($id): JsonResponse
    {
        $invoice = CertifyInvoices::find($id);

        $invoice->amount_letter = $this->convertAmoutToLetter(($invoice->amount*1.19));

        $clients = CertifyClient::all();

        $products = CertifyProduct::getAllProductsFormatted();

        return response()->json(["invoice" => $invoice, "clients"=>$clients, "products"=>$products, "unSelectedProducts"=>$products]);
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
        $client = $invoiceData['client'];

        $fac_id = $invoiceData['fac_id'];

         $invoice = CertifyInvoices::create([
            'fac_id' => $fac_id,
            'date' => $invoiceData['date'],
            'client_id' => $client['id'],
            'amount' => $invoiceData['total'] ?? $invoiceData['amount'] ?? 0,
            'payment_type' => $invoiceData['payment_type'],
            'tva_rate' => $invoiceData['tva_rate'] ?? null,
            'tva_amount' => $invoiceData['tva_amount'] ?? null,
            'ht_amount' => $invoiceData['ht_amount'] ?? null,
            'timbre_rate' => $invoiceData['timbre_rate'] ?? null,
            'timbre_amount' => $invoiceData['timbre_amount'] ?? null,
            'cheque_number' => $invoiceData['cheque_number'] ?? null,
            'cheque_id' => $invoiceData['cheque_id'] ?? null,
        ]);

        $products = $invoiceData['certify_invoice_products'];

        foreach ($products as $product) {

            CertifyInvoiceProducts::create([
               'product_id' => $product['product']['id'],
               'price' => $product['price'],
               'quantity' => $product['quantity'],
               'total' => $product['quantity'] * $product['price'],
               'certify_invoice_id' => $invoice->id,
            ]);
        }



        return response()->json(['message' => 'Product created successfully', "id"=>$invoice->id]);

    }

    /**
     * get List Of Clients and Products
     *
     * @return JsonResponse
     */

    #[OA\Get(
        path: "/api/certifyInvoices/getInvoiceData",
        operationId: "getInvoiceData",
        description: "Returns the list of clients and products",
        tags: ["certifyInvoice"],
    )]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        type: 'object'
    )])]
    public function getInvoiceData(): JsonResponse {
        $clients = CertifyClient::all();
        $products = CertifyProduct::getAllProductsFormatted();
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

        $max_fac_id = CertifyInvoices::whereYear('date',$year)->max('fac_id');

        return ($max_fac_id ?? 0) + 1;
    }



    public function update(Request $request): JsonResponse
    {
        $invoiceData = $request->input('invoiceData');

        $client = $invoiceData['client'];

       $fac_id = $invoiceData['fac_id'];

       $invoice = CertifyInvoices::find($invoiceData['id']);

         $invoice->update([
             'date' => $invoiceData['date'],
             'client_id' => $client['id'],
             'amount' => $invoiceData['total'] ?? $invoiceData['amount'] ?? 0,
             'payment_type' => $invoiceData['payment_type'],
             'tva_rate' => $invoiceData['tva_rate'] ?? null,
             'tva_amount' => $invoiceData['tva_amount'] ?? null,
             'ht_amount' => $invoiceData['ht_amount'] ?? null,
             'timbre_rate' => $invoiceData['timbre_rate'] ?? null,
             'timbre_amount' => $invoiceData['timbre_amount'] ?? null,
             'cheque_number' => $invoiceData['cheque_number'] ?? null,
             'cheque_id' => $invoiceData['cheque_id'] ?? null,
         ]);


        foreach ($invoice->certifyInvoiceProducts as $product) {
            $product->delete();
        }

        $products = $invoiceData['certify_invoice_products'];

         foreach ($products as $product) {

             CertifyInvoiceProducts::create([
                 'product_id' => $product['product']['id'],
                 'price' => $product['price'],
                 'quantity' => $product['quantity'],
                 'total' => $product['price'] * $product['quantity'],
                 'certify_invoice_id' => $invoiceData['id'],
             ]);
         }

        return response()->json(["message"=>"Invoice Updated Successfully"]);

    }

    /**
     * Delete a certify invoice
     *
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Delete(
        path: "/api/certifyInvoices/delete/{id}",
        operationId: "deleteCertifyInvoice",
        description: "Delete a certify invoice",
        tags: ["certifyInvoice"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: "Successfully deleted")]
    public function delete(int $id): JsonResponse
    {
        $invoice = CertifyInvoices::findOrFail($id);
        
        // Delete associated products
        $invoice->certifyInvoiceProducts()->delete();
        
        $invoice->delete();
        
        return response()->json(["message" => "Certify Invoice deleted successfully"]);
    }

}
