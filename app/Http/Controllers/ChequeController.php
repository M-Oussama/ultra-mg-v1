<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class ChequeController extends Controller
{
    #[OA\Get(
        path: "/api/cheques/list",
        summary: "List all cheques",
        tags: ["Cheques"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of cheques",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/ICheque"))
            )
        ]
    )]
    public function getCheques()
    {
        $cheques = Cheque::with('client')->get();
        return response()->json($cheques);
    }

    #[OA\Post(
        path: "/api/cheques/store",
        summary: "Store a new cheque",
        tags: ["Cheques"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["cheque_date", "cheque_number", "client_id"],
                    properties: [
                        new OA\Property(property: "cheque_date", type: "string", format: "date"),
                        new OA\Property(property: "cheque_number", type: "string"),
                        new OA\Property(property: "client_id", type: "integer"),
                        new OA\Property(property: "file", type: "string", format: "binary", description: "PDF scan of the cheque")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Cheque created successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
        public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cheque_date' => 'required|date',
            'cheque_number' => 'required|string',
            'client_id' => 'required|exists:certify_clients,id',
            'file' => 'nullable|mimes:pdf|max:10240', // Max 10MB PDF
            'banque' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['cheque_date', 'cheque_number', 'client_id', 'amount', 'banque']);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('cheques/scans', 'public');
            $data['file_path'] = $path;
        }

        $cheque = Cheque::create($data);

        return response()->json($cheque, 201);
    }

    #[OA\Post(
        path: "/api/cheques/update/{id}",
        summary: "Update an existing cheque",
        tags: ["Cheques"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "cheque_date", type: "string", format: "date"),
                        new OA\Property(property: "cheque_number", type: "string"),
                        new OA\Property(property: "client_id", type: "integer"),
                        new OA\Property(property: "file", type: "string", format: "binary")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Cheque updated successfully"),
            new OA\Response(response: 404, description: "Cheque not found")
        ]
    )]
        public function update(Request $request, $id)
    {
        $cheque = Cheque::find($id);
        if (!$cheque) {
            return response()->json(['message' => 'Cheque not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'cheque_date' => 'sometimes|date',
            'cheque_number' => 'sometimes|string',
            'client_id' => 'sometimes|exists:certify_clients,id',
            'file' => 'nullable|mimes:pdf|max:10240',
            'banque' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['cheque_date', 'cheque_number', 'client_id', 'amount', 'banque']);

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($cheque->file_path) {
                Storage::disk('public')->delete($cheque->file_path);
            }
            $path = $request->file('file')->store('cheques/scans', 'public');
            $data['file_path'] = $path;
        }

        $cheque->update($data);

        return response()->json($cheque);
    }

    #[OA\Delete(
        path: "/api/cheques/delete/{id}",
        summary: "Delete a cheque",
        tags: ["Cheques"],
        responses: [
            new OA\Response(response: 200, description: "Cheque deleted successfully"),
            new OA\Response(response: 404, description: "Cheque not found")
        ]
    )]
    public function delete($id)
    {
        $cheque = Cheque::find($id);
        if (!$cheque) {
            return response()->json(['message' => 'Cheque not found'], 404);
        }

        // Check if cheque is used in certify_invoices
        $usedInCertify = \App\Models\CertifyInvoices::where('cheque_id', $cheque->id)
            ->orWhere(function($query) use ($cheque) {
                $query->whereNotNull('cheque_number')->where('cheque_number', $cheque->cheque_number);
            })->exists();
            
        if ($usedInCertify) {
            return response()->json(['message' => 'Cannot delete this cheque. It is used by one or more certify invoices.'], 422);
        }

        // Check if cheque is used in sub_certify_invoices
        $usedInSub = \App\Models\SubCertifyInvoices::where('cheque_id', $cheque->id)
            ->orWhere(function($query) use ($cheque) {
                $query->whereNotNull('cheque_number')->where('cheque_number', $cheque->cheque_number);
            })->exists();
            
        if ($usedInSub) {
            return response()->json(['message' => 'Cannot delete this cheque. It is used by one or more sub-certify invoices.'], 422);
        }

        $cheque->delete();
        return response()->json(['message' => 'Cheque deleted successfully']);
    }

    public function getStatus(Request $request, $chequeId)
    {
        return $this->getStatusExcluding($request, $chequeId, null);
    }

    public function getStatusExcluding(Request $request, $chequeId, $excludeCommandId = null)
    {
        $cheque = Cheque::find($chequeId);
        if (!$cheque) return response()->json(['message' => 'Cheque not found'], 404);

        $usedCertify = \App\Models\CertifyInvoices::where(function($q) use ($cheque) {
            $q->where('cheque_id', $cheque->id)
              ->orWhere(function($sq) use ($cheque) {
                  $sq->whereNotNull('cheque_number')->where('cheque_number', $cheque->cheque_number);
              });
        });
        
        $usedSub = \App\Models\SubCertifyInvoices::where(function($q) use ($cheque) {
            $q->where('cheque_id', $cheque->id)
              ->orWhere(function($sq) use ($cheque) {
                  $sq->whereNotNull('cheque_number')->where('cheque_number', $cheque->cheque_number);
              });
        });

        if ($excludeCommandId) {
            $type = $request->query('type');
            if ($type === 'sub') {
                $usedSub->where('id', '!=', $excludeCommandId);
            } elseif ($type === 'certify') {
                $usedCertify->where('id', '!=', $excludeCommandId);
            } else {
                // If type is not specified, we assume the exclude applies to both if IDs match (unlikely to collide but safe)
                $usedCertify->where('id', '!=', $excludeCommandId);
                $usedSub->where('id', '!=', $excludeCommandId);
            }
        }

        $usedAmount = $usedCertify->sum('amount') + $usedSub->sum('amount');
        $remaining = max(0, $cheque->amount - $usedAmount);

        return response()->json([
            'used_amount' => (float)$usedAmount,
            'remaining_balance' => (float)$remaining,
            'is_available' => ((float)$remaining > 0),
            'cheque_amount' => (float)$cheque->amount,
        ]);
    }

    public function getAvailable()
    {
        $cheques = Cheque::with('client')->get()->map(function($cheque) {
            $usedCertify = \App\Models\CertifyInvoices::where(function($q) use ($cheque) {
                $q->where('cheque_id', $cheque->id)
                  ->orWhere(function($sq) use ($cheque) {
                      $sq->whereNotNull('cheque_number')->where('cheque_number', $cheque->cheque_number);
                  });
            })->sum('amount');
            
            $usedSub = \App\Models\SubCertifyInvoices::where(function($q) use ($cheque) {
                $q->where('cheque_id', $cheque->id)
                  ->orWhere(function($sq) use ($cheque) {
                      $sq->whereNotNull('cheque_number')->where('cheque_number', $cheque->cheque_number);
                  });
            })->sum('amount');
            
            $used = $usedCertify + $usedSub;
            $cheque->used_amount = $used;
            $cheque->remaining_balance = max(0, $cheque->amount - $used);
            return $cheque;
        })->filter(function($cheque) {
            return $cheque->remaining_balance > 0;
        })->values();

        return response()->json($cheques);
    }
}
