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

    public function scan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $apiKey = $request->header('X-Gemini-API-Key') ?? env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['message' => 'Gemini API Key not configured. Please provide it in settings or .env'], 500);
        }

        $file = $request->file('file');
        $base64Data = base64_encode(file_get_contents($file->path()));

        try {
            $response = \Illuminate\Support\Facades\Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "Analyze this Algerian cheque.
                            IMPORTANT: 'SATIS Detergents' or 'SETIFIS Detergents' is MY company (the beneficiary). DO NOT return it as the client_name.
                            Find the CLIENT name (the person or company paying). Look for names near 'Pour compte de', 'Tiré par', or the issuer's signature area.
                            
                            Return a JSON object with:
                            {
                              \"cheque_number\": \"string\",
                              \"amount\": \"number (numeric only)\",
                              \"date\": \"YYYY-MM-DD\",
                              \"bank\": \"Short code like CPA, BDL, BEA, BNA, BADR, SOCIETE GENERALE, GULF BANK, etc.\",
                              \"client_name\": \"The name of the PERSON or COMPANY who issued the cheque (NOT SATIS Detergents)\"
                            }"],
                            [
                                'inline_data' => [
                                    'mime_type' => 'application/pdf',
                                    'data' => $base64Data
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json'
                ]
            ]);

            if ($response->failed()) {
                return response()->json(['message' => 'AI Service error: ' . $response->body()], 502);
            }

            $data = json_decode($response->json('candidates.0.content.parts.0.text'), true);
            
            if (!$data) {
                return response()->json(['message' => 'Failed to parse AI response'], 500);
            }

            // 4. Try to find the client in certify_clients with improved fuzzy matching
            $matchedClient = null;
            if (isset($data['client_name'])) {
                $rawName = trim($data['client_name']);
                
                // Remove common business prefixes/suffixes for better matching
                $cleanName = preg_replace('/^(EURL|SARL|SPA|SNC|GROUP|GROUPE)\s+/i', '', $rawName);
                $cleanName = preg_replace('/\s+(EURL|SARL|SPA|SNC)$/i', '', $cleanName);
                $cleanName = trim($cleanName);

                // Try Exact Match first
                $matchedClient = \Illuminate\Support\Facades\DB::table('certify_clients')
                    ->where('name', 'LIKE', "%{$cleanName}%")
                    ->orWhere('surname', 'LIKE', "%{$cleanName}%")
                    ->first();

                // If no match, try matching just the first word (most significant)
                if (!$matchedClient) {
                    $firstWord = explode(' ', $cleanName)[0];
                    if (strlen($firstWord) > 3) {
                        $matchedClient = \Illuminate\Support\Facades\DB::table('certify_clients')
                            ->where('name', 'LIKE', "%{$firstWord}%")
                            ->orWhere('surname', 'LIKE', "%{$firstWord}%")
                            ->first();
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => array_merge($data, [
                    'matched_client_id' => $matchedClient?->id,
                    'matched_client' => $matchedClient
                ])
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Scan error: ' . $e->getMessage()], 500);
        }
    }
}
