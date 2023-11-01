<?php

namespace App\Http\Controllers;

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

        $fac_id = 1;

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

        return response()->json(['message' => 'Product created successfully']);

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

        return response()->json(["clients" => $clients, "products" => $products]);
    }
}
