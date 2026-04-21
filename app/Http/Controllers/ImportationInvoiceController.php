<?php

namespace App\Http\Controllers;

use App\Models\ImportationInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\Log;

class ImportationInvoiceController extends Controller
{
    #[OA\Get(
        path: "/api/importation-invoices/list",
        summary: "List all importation invoices",
        tags: ["Importation Invoices"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of importation invoices",
                content: new OA\JsonContent(type: "array", items: new OA\Items(type: "object"))
            )
        ]
    )]
    public function list()
    {
        $invoices = ImportationInvoice::with('supplier')->get();
        return response()->json($invoices);
    }

    #[OA\Post(
        path: "/api/importation-invoices/store",
        summary: "Store a new importation invoice",
        tags: ["Importation Invoices"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["supplier_id", "invoice_number", "BL_number", "company_name", "start_date", "arrive_date", "amount", "container_status"],
                    properties: [
                        new OA\Property(property: "supplier_id", type: "integer"),
                        new OA\Property(property: "invoice_number", type: "string"),
                        new OA\Property(property: "BL_number", type: "string"),
                        new OA\Property(property: "company_name", type: "string"),
                        new OA\Property(property: "start_date", type: "string", format: "date"),
                        new OA\Property(property: "arrive_date", type: "string", format: "date"),
                        new OA\Property(property: "amount", type: "number", format: "float"),
                        new OA\Property(property: "container_status", type: "string"),
                        new OA\Property(property: "notes", type: "string"),
                        new OA\Property(property: "vessel_name", type: "string"),
                        new OA\Property(property: "vessel_number", type: "string"),
                        new OA\Property(property: "container_number", type: "string"),
                        new OA\Property(property: "file", type: "string", format: "binary", description: "PDF scan of the invoice")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Invoice created successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string',
            'BL_number' => 'required|string',
            'company_name' => 'required|string',
            'start_date' => 'required|date',
            'arrive_date' => 'required|date',
            'amount' => 'required|numeric',
            'container_status' => 'required|string',
            'notes' => 'nullable|string',
            'vessel_name' => 'nullable|string',
            'vessel_number' => 'nullable|string',
            'container_number' => 'nullable|string',
            'file' => 'nullable|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'supplier_id', 'invoice_number', 'BL_number', 'company_name',
            'start_date', 'arrive_date', 'amount', 'container_status', 'notes',
            'vessel_name', 'vessel_number', 'container_number'
        ]);

        $invoice = ImportationInvoice::create($data);

        if ($request->hasFile('file')) {
            $invoice->addMediaFromRequest('file')->toMediaCollection('invoice_pdf');
        }

        return response()->json($invoice, 201);
    }

    #[OA\Post(
        path: "/api/importation-invoices/update/{id}",
        summary: "Update an existing importation invoice",
        tags: ["Importation Invoices"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "supplier_id", type: "integer"),
                        new OA\Property(property: "invoice_number", type: "string"),
                        new OA\Property(property: "BL_number", type: "string"),
                        new OA\Property(property: "company_name", type: "string"),
                        new OA\Property(property: "start_date", type: "string", format: "date"),
                        new OA\Property(property: "arrive_date", type: "string", format: "date"),
                        new OA\Property(property: "amount", type: "number", format: "float"),
                        new OA\Property(property: "container_status", type: "string"),
                        new OA\Property(property: "notes", type: "string"),
                        new OA\Property(property: "vessel_name", type: "string"),
                        new OA\Property(property: "vessel_number", type: "string"),
                        new OA\Property(property: "container_number", type: "string"),
                        new OA\Property(property: "file", type: "string", format: "binary")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Invoice updated successfully"),
            new OA\Response(response: 404, description: "Invoice not found")
        ]
    )]
    public function update(Request $request, $id)
    {
        $invoice = ImportationInvoice::find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'invoice_number' => 'sometimes|string',
            'BL_number' => 'sometimes|string',
            'company_name' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'arrive_date' => 'sometimes|date',
            'amount' => 'sometimes|numeric',
            'container_status' => 'sometimes|string',
            'notes' => 'nullable|string',
            'vessel_name' => 'nullable|string',
            'vessel_number' => 'nullable|string',
            'container_number' => 'nullable|string',
            'file' => 'nullable|mimes:pdf|max:10240',
            'deleted_attachments' => 'nullable|array',
            'deleted_attachments.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Log::info($request->all());

        $data = $request->only([
            'supplier_id', 'invoice_number', 'BL_number', 'company_name',
            'vessel_name','vessel_number','container_number',
            'start_date', 'arrive_date', 'amount', 'container_status', 'notes'
        ]);

        $invoice->update($data);

        if ($request->has('deleted_attachments')) {
            foreach ($request->input('deleted_attachments') as $url) {
                $media = $invoice->getMedia('invoice_pdf')->first(function ($item) use ($url) {
                    return $item->getUrl() === $url || $item->getFullUrl() === $url;
                });
                if ($media) {
                    $media->delete();
                }
            }
        }

        if ($request->hasFile('file')) {
            $invoice->clearMediaCollection('invoice_pdf');
            $invoice->addMediaFromRequest('file')->toMediaCollection('invoice_pdf');
        }

        return response()->json($invoice);
    }

    #[OA\Delete(
        path: "/api/importation-invoices/delete/{id}",
        summary: "Delete an importation invoice",
        tags: ["Importation Invoices"],
        responses: [
            new OA\Response(response: 200, description: "Invoice deleted successfully"),
            new OA\Response(response: 404, description: "Invoice not found")
        ]
    )]
    public function delete($id)
    {
        $invoice = ImportationInvoice::find($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        // Spatie Media Library automatically deletes media on model delete
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }
}
