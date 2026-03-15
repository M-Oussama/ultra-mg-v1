<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\CertifyInvoices;
use App\Models\Company;
use App\Http\Helpers\NumberToLetter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function exportSale($saleId)
    {
        $sale = Sale::with(['client', 'saleItems.product'])->findOrFail($saleId);
        $company = Company::first();
        
        $amountLetter = $this->convertAmoutToLetter($sale->total_amount * 1.19);
        
        $pdf = Pdf::loadView('sale_pdf', compact('sale', 'company', 'amountLetter'));
        
        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
            
        return $pdf->stream('Facture_' . $sale->id . '.pdf');
    }

    public function exportCertifyInvoice($invoiceId)
    {
        $invoice = CertifyInvoices::with(['client', 'certifyInvoiceProducts.product'])->findOrFail($invoiceId);
        $company = Company::first();
        
        $totalTTC = $invoice->amount + ($invoice->tva_amount ?: ($invoice->amount * 0.19)) + ($invoice->timbre_amount ?: 0);
        $amountLetter = $this->convertAmoutToLetter($totalTTC);
        
        $pdf = Pdf::loadView('certify_invoice_pdf', compact('invoice', 'company', 'amountLetter'));
        
        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
            
        return $pdf->stream('Facture_Certifiee_' . $invoice->fac_id . '.pdf');
    }
    public function exportMultiSales(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return response()->json(['error' => 'No IDs provided'], 400);
        }

        $sales = Sale::with(['client', 'saleItems.product'])->whereIn('id', $ids)->get();
        $company = Company::first();

        foreach ($sales as $sale) {
            $sale->amountLetter = $this->convertAmoutToLetter($sale->total_amount * 1.19);
        }

        $pdf = Pdf::loadView('multi_sale_pdf', compact('sales', 'company'));

        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream('Factures_Multiples.pdf');
    }

    public function exportMultiCertifyInvoices(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return response()->json(['error' => 'No IDs provided'], 400);
        }

        $invoices = CertifyInvoices::with(['client', 'certifyInvoiceProducts.product'])->whereIn('id', $ids)->get();
        $company = Company::first();

        foreach ($invoices as $invoice) {
            $totalTTC = $invoice->amount + ($invoice->tva_amount ?: ($invoice->amount * 0.19)) + ($invoice->timbre_amount ?: 0);
            $invoice->amountLetter = $this->convertAmoutToLetter($totalTTC);
        }

        $pdf = Pdf::loadView('multi_certify_invoice_pdf', compact('invoices', 'company'));

        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream('Factures_Certifiees_Multiples.pdf');
    }
    public function generateCustomerLog()
    {
    }
}
