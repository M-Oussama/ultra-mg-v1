<?php

namespace App\Http\Controllers;

use App\Models\CertifyInvoices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CertifyInvoiceController extends Controller
{

    /**
     * get List Of All Invoices
     *
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
    public function getInvoices(): JsonResponse
    {
        $invoices = CertifyInvoices::all();
        return response()->json($invoices);
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

    public function store(Request $request) {

        $validatedData = $request->validate([
            'client_id' => 'required|integer',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'payment_type' => 'required|integer',
        ]);

        //$product = CertifyInvoices::create($validatedData);

        return response()->json(['message' => 'Product created successfully', 'product' => $validatedData]);

    }
}
