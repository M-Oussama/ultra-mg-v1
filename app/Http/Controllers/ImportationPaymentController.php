<?php

namespace App\Http\Controllers;

use App\Models\ImportationPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\Auth;

class ImportationPaymentController extends Controller
{
    #[OA\Get(
        path: "/api/importation-payments/list/{invoice_id}",
        summary: "List all importation payments",
        tags: ["Importation Payments"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of importation payments",
                content: new OA\JsonContent(type: "array", items: new OA\Items(type: "object"))
            )
        ]
    )]
    public function list(Request $request, $invoice_id = null)
    {
        $query = ImportationPayment::with(['invoice', 'media']);
        $from = $request->input('from', '');
        $to = $request->input('to', '');
        
        if ($invoice_id) {
            $query->where('importation_invoice_id', $invoice_id);
        }

        // Hierarchical Data Isolation
        $user = Auth::user();
        if ($user && !$user->isGlobalAdmin()) {
            $query->whereHas('invoice', function($q) use ($user) {
                if ($user->isDepartmentManager()) {
                    $deptIds = $user->departments->pluck('id')->toArray();
                    $q->whereHas('user', function($uq) use ($deptIds) {
                        $uq->whereHas('departments', function($sq) use ($deptIds) {
                            $sq->whereIn('departments.id', $deptIds);
                        });
                    });
                } else {
                    $q->where('user_id', $user->id);
                }
            });
        }

        if ($from !== '') {
            $query->whereDate('payment_date', '>=', $from);
        }

        if ($to !== '') {
            $query->whereDate('payment_date', '<=', $to);
        }
        
        $payments = $query->get();
        
        // Map media URLs to the response
        $payments->each(function ($payment) {
            $payment->pdf_urls = $payment->getMedia('scans')->map(function ($media) {
                return $media->getFullUrl();
            });
        });

        return response()->json($payments);
    }

    #[OA\Post(
        path: "/api/importation-payments/store",
        summary: "Store a new importation payment",
        tags: ["Importation Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["importation_invoice_id", "amount", "type", "payment_date"],
                    properties: [
                        new OA\Property(property: "importation_invoice_id", type: "integer"),
                        new OA\Property(property: "amount", type: "number", format: "float"),
                        new OA\Property(property: "type", type: "string", description: "invoice_settlement, customs fee, freight/transit"),
                        new OA\Property(property: "payment_date", type: "string", format: "date"),
                        new OA\Property(property: "notes", type: "string"),
                        new OA\Property(property: "files[]", type: "array", items: new OA\Items(type: "string", format: "binary"), description: "One or many PDF files")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Payment created successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'importation_invoice_id' => 'required|exists:importation_invoices,id',
            'amount' => 'required|numeric',
            'type' => 'required|string|in:invoice_settlement,customs fee,freight/transit',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = ImportationPayment::create($request->only([
            'importation_invoice_id', 'amount', 'type', 'payment_date', 'notes'
        ]));

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $payment->addMedia($file)->toMediaCollection('scans');
            }
        }

        return response()->json($payment->load('media'), 201);
    }

    #[OA\Post(
        path: "/api/importation-payments/update/{id}",
        summary: "Update an existing importation payment",
        tags: ["Importation Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "importation_invoice_id", type: "integer"),
                        new OA\Property(property: "amount", type: "number", format: "float"),
                        new OA\Property(property: "type", type: "string"),
                        new OA\Property(property: "payment_date", type: "string", format: "date"),
                        new OA\Property(property: "notes", type: "string"),
                        new OA\Property(property: "files[]", type: "array", items: new OA\Items(type: "string", format: "binary"))
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Payment updated successfully"),
            new OA\Response(response: 404, description: "Payment not found")
        ]
    )]
    public function update(Request $request, $id)
    {
        $payment = ImportationPayment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'importation_invoice_id' => 'sometimes|exists:importation_invoices,id',
            'amount' => 'sometimes|numeric',
            'type' => 'sometimes|string|in:invoice_settlement,customs fee,freight/transit',
            'payment_date' => 'sometimes|date',
            'notes' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment->update($request->only([
            'importation_invoice_id', 'amount', 'type', 'payment_date', 'notes'
        ]));

        if ($request->hasFile('files')) {
            // Note: This implementation appends new files. 
            // If the user wants to REPLACE files, they should delete them first or we should clear the collection.
            // Given the requirement "one or many", appending seems safer unless told otherwise.
            foreach ($request->file('files') as $file) {
                $payment->addMedia($file)->toMediaCollection('scans');
            }
        }

        return response()->json($payment->load('media'));
    }

    #[OA\Delete(
        path: "/api/importation-payments/delete/{id}",
        summary: "Delete an importation payment",
        tags: ["Importation Payments"],
        responses: [
            new OA\Response(response: 200, description: "Payment deleted successfully"),
            new OA\Response(response: 404, description: "Payment not found")
        ]
    )]
    public function delete($id)
    {
        $payment = ImportationPayment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->clearMediaCollection('scans');
        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }
}
