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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['cheque_date', 'cheque_number', 'client_id']);

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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['cheque_date', 'cheque_number', 'client_id']);

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

        $cheque->delete();
        return response()->json(['message' => 'Cheque deleted successfully']);
    }
}
