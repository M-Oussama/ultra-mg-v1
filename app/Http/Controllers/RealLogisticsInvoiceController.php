<?php

namespace App\Http\Controllers;

use App\Models\RealLogisticsInvoice;
use App\Models\RealLogisticsItemsInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Client;

class RealLogisticsInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $clientId = $request->input('client_id');
        $status = $request->input('status');
        $from = $request->input('from');
        $to = $request->input('to');

        $query = RealLogisticsInvoice::query();

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($from && $to) {
            $query->whereBetween('invoice_date', [$from, $to]);
        } elseif ($from) {
            $query->where('invoice_date', '>=', $from);
        }

        $query->orderBy('invoice_date', 'desc');

        $paginated = $query->paginate($perPage, ['*'], 'page', $currentPage);

        return response()->json([
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'totalPage' => $paginated->lastPage(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required_without:items.*.product_id|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $totalAmount = 0;
                foreach ($validated['items'] as $item) {
                    $totalAmount += $item['quantity'] * $item['price'];
                }

                $invoice = RealLogisticsInvoice::create([
                    'invoice_date' => $validated['invoice_date'],
                    'client_id' => $validated['client_id'],
                    'total_amount' => $totalAmount,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'completed'
                ]);

                foreach ($validated['items'] as $item) {
                    RealLogisticsItemsInvoice::create([
                        'real_logistics_invoice_id' => $invoice->id,
                        'product_id' => $item['product_id'] ?? null,
                        'product_name' => $item['product_name'] ?? null,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total_price' => $item['quantity'] * $item['price'],
                    ]);
                }

                return response()->json([
                    'message' => 'Real Logistics Invoice created successfully',
                    'invoice' => $invoice->load('items')
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating invoice: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RealLogisticsInvoice $realLogisticsInvoice): JsonResponse
    {
        return response()->json($realLogisticsInvoice->load(['client', 'items.product']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RealLogisticsInvoice $realLogisticsInvoice): JsonResponse
    {
        $validated = $request->validate([
            'invoice_date' => 'sometimes|required|date',
            'client_id' => 'sometimes|required|exists:clients,id',
            'notes' => 'nullable|string',
            'status' => 'sometimes|required|string',
            'items' => 'sometimes|required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required_without:items.*.product_id|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($validated, $realLogisticsInvoice, $request) {
                if (isset($validated['items'])) {
                    $totalAmount = 0;
                    foreach ($validated['items'] as $item) {
                        $totalAmount += $item['quantity'] * $item['price'];
                    }
                    $realLogisticsInvoice->total_amount = $totalAmount;
                }

                $realLogisticsInvoice->fill($request->only(['invoice_date', 'client_id', 'notes', 'status']));
                $realLogisticsInvoice->save();

                if (isset($validated['items'])) {
                    // Delete existing items
                    $realLogisticsInvoice->items()->delete();

                    // Create new items
                    foreach ($validated['items'] as $item) {
                        RealLogisticsItemsInvoice::create([
                            'real_logistics_invoice_id' => $realLogisticsInvoice->id,
                            'product_id' => $item['product_id'] ?? null,
                            'product_name' => $item['product_name'] ?? null,
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'total_price' => $item['quantity'] * $item['price'],
                        ]);
                    }
                }

                return response()->json([
                    'message' => 'Real Logistics Invoice updated successfully',
                    'invoice' => $realLogisticsInvoice->load('items')
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating invoice: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RealLogisticsInvoice $realLogisticsInvoice): JsonResponse
    {
        try {
            return DB::transaction(function () use ($realLogisticsInvoice) {
                $realLogisticsInvoice->items()->delete();
                $realLogisticsInvoice->delete();

                return response()->json([
                    'message' => 'Real Logistics Invoice deleted successfully'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting invoice: ' . $e->getMessage()], 500);
        }
    }
    public function previewPdf(Request $request)
    {
        $data = $request->all();
        $dbCompany = Company::first();
        $dbClient = isset($data['client_id']) ? Client::find($data['client_id']) : null;

        $company = $dbCompany ? (object) array_merge($dbCompany->toArray(), $data['issuer_data'] ?? []) : (object)($data['issuer_data'] ?? []);
        $client = $dbClient ? (object) array_merge($dbClient->toArray(), $data['client_data'] ?? []) : (object)($data['client_data'] ?? []);


        // 1. Calculate the HT Total
    $total_ht = collect($data['items'])->sum(function($item) {
        return $item['price'] * $item['quantity'];
    });

    // 2. Calculate the TTC (HT + 19% TVA)
    $total_ttc = $total_ht * 1.19;


        // 3. Convert TTC to French Words
       $total_in_words = $this->numberToFrenchWords($total_ttc);
        // Mocking an invoice object for the view
        $invoice = (object)[
            'invoice_date' => $data['invoice_date'] ?? now()->format('Y-m-d'),
'id' => (isset($data['invoice_date']) ? \Carbon\Carbon::parse($data['invoice_date'])->format('Y') : now()->format('Y')) . '/' . str_pad($data['id'] ?? '', 2, '0', STR_PAD_LEFT),
            'client' => $client,
            'notes' => $data['notes'] ?? '',
            'items' => collect($data['items'])->map(function($item) {
                return (object)[
                    'product_name' => $item['product_name'] ?? ($item['product_id'] ? \App\Models\Product::find($item['product_id'])->name : 'Produit'),
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['price'] * $item['quantity']
                ];
            }),
            'total_amount' => collect($data['items'])->sum(function($item) {
                return $item['price'] * $item['quantity'];
            })
        ];

        $pdf = Pdf::loadView('real_logistics_invoice_pdf',  compact('invoice', 'company', 'total_in_words'));
        return $pdf->stream('Preview_Logistics_Invoice.pdf');
    }


    // Simple Helper to avoid the Undefined Variable error
    private function numberToFrenchWords($number)
    {
        $f = new \NumberFormatter("fr", \NumberFormatter::SPELLOUT);
        return ucfirst($f->format($number));
    }
    public function exportPdf($id)
    {
        $invoice = RealLogisticsInvoice::with(['client', 'items'])->findOrFail($id);
        $company = Company::first();

        // Calculate TTC and words for the view
        $total_ttc = $invoice->total_amount * 1.19;
        $total_in_words = $this->numberToFrenchWords($total_ttc);

        $pdf = Pdf::loadView('real_logistics_invoice_pdf', compact('invoice', 'company', 'total_in_words'));
        return $pdf->stream('Logistics_Invoice_' . $invoice->id . '.pdf');
    }
}

