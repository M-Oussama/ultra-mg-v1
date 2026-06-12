<?php

namespace App\Http\Controllers;

use App\Models\RealLogisticsInvoice;
use App\Models\RealLogisticsItemsInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'company_id' => 'nullable|exists:companies,id',
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
                    'company_id' => $validated['company_id'] ?? null,
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
        return response()->json($realLogisticsInvoice->load(['client', 'company', 'items.product']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RealLogisticsInvoice $realLogisticsInvoice): JsonResponse
    {
        $validated = $request->validate([
            'invoice_date' => 'sometimes|required|date',
            'client_id' => 'sometimes|required|exists:clients,id',
            'company_id' => 'sometimes|nullable|exists:companies,id',
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
                if (array_key_exists('company_id', $validated)) {
                    $realLogisticsInvoice->company_id = $validated['company_id'];
                }
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
    public function previewPdf(Request $request, $id = null)
    {
        $invoiceId = $id
            ?? $request->input('invoice_id')
            ?? $request->input('real_logistics_invoice_id')
            ?? $request->input('invoiceId');

        if (!$invoiceId) {
            $draft = $this->buildDraftPreviewInvoice($request);
            $company = $this->buildDraftPreviewCompany($request);

            return $this->streamInvoicePdf($draft, $company, 'Preview_Logistics_Invoice.pdf');
        }

        $invoice = RealLogisticsInvoice::with(['client', 'company', 'items.product'])->findOrFail($invoiceId);

        return $this->streamInvoicePdf($invoice, $this->resolveInvoiceCompany($invoice), 'Preview_Logistics_Invoice.pdf');
    }


    // Simple Helper to avoid the Undefined Variable error
    private function numberToFrenchWords($number)
    {
        $f = new \NumberFormatter("fr", \NumberFormatter::SPELLOUT);
        return ucfirst($f->format($number));
    }
    public function exportPdf($id)
    {
        $invoice = RealLogisticsInvoice::with(['client', 'company', 'items.product'])->findOrFail($id);

        return $this->streamInvoicePdf($invoice, $this->resolveInvoiceCompany($invoice), 'Logistics_Invoice_' . $invoice->id . '.pdf');
    }

    private function streamInvoicePdf($invoice, $company = null, string $filename = 'Logistics_Invoice.pdf')
    {
        if ($invoice instanceof RealLogisticsInvoice) {
            $invoice->loadMissing(['client', 'company', 'items.product']);
        }
        $company = $company ?: (object) [
            'name' => '',
            'email' => '',
            'NART' => '',
            'NRC' => '',
            'NIS' => '',
            'NIF' => '',
        ];

        $totalAmount = data_get($invoice, 'total_amount');

        if ($totalAmount === null) {
            $items = collect(data_get($invoice, 'items', []));
            $totalAmount = $items->sum(function ($item) {
                return (float) ($item->total_price ?? ((float) $item->quantity * (float) $item->price));
            });
        }

        if ($invoice instanceof RealLogisticsInvoice) {
            $invoice->total_amount = $totalAmount;
        } else {
            $invoice->total_amount = $totalAmount;
        }

        $total_ttc = $totalAmount * 1.19;
        $total_in_words = $this->numberToFrenchWords($total_ttc);

        $pdf = Pdf::loadView('real_logistics_invoice_pdf', compact('invoice', 'company', 'total_in_words'));
        return $pdf->stream($filename);
    }

    private function resolveInvoiceCompany(RealLogisticsInvoice $invoice): object
    {
        if ($invoice->relationLoaded('company') && $invoice->company) {
            return $invoice->company;
        }

        if (!empty($invoice->company_id)) {
            $company = Company::find($invoice->company_id);
            if ($company) {
                return $company;
            }
        }

        return Company::first() ?: (object) [
            'name' => '',
            'email' => '',
            'NART' => '',
            'NRC' => '',
            'NIS' => '',
            'NIF' => '',
        ];
    }

    private function buildDraftPreviewCompany(Request $request): object
    {
        $dbCompany = Company::first();
        $issuerData = $request->input('issuer_data', []);

        return $dbCompany
            ? (object) array_merge($dbCompany->toArray(), $issuerData)
            : (object) $issuerData;
    }

    private function buildDraftPreviewInvoice(Request $request): object
    {
        $data = $request->all();
        $clientData = $data['client_data'] ?? [];
        $items = collect($data['items'] ?? [])->map(function ($item) {
            return (object) [
                'product_name' => $item['product_name'] ?? 'Produit',
                'price' => (float) ($item['price'] ?? 0),
                'quantity' => (float) ($item['quantity'] ?? 0),
                'total_price' => (float) ($item['price'] ?? 0) * (float) ($item['quantity'] ?? 0),
            ];
        });

        $client = (object) array_merge([
            'name' => '',
            'surname' => '',
            'address' => '',
            'NRC' => '',
            'NIF' => '',
            'NART' => '',
            'NIS' => '',
        ], is_array($clientData) ? $clientData : []);

        $totalAmount = $items->sum(function ($item) {
            return (float) $item->total_price;
        });

        return (object) [
            'invoice_date' => $data['invoice_date'] ?? now()->format('Y-m-d'),
            'id' => $data['id'] ?? ('PREVIEW/' . now()->format('YmdHis')),
            'client' => $client,
            'notes' => $data['notes'] ?? '',
            'items' => $items,
            'total_amount' => $totalAmount,
        ];
    }
}

