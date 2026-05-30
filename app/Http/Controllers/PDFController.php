<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\CertifyInvoices;
use App\Models\Company;
use App\Http\Helpers\NumberToLetter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PDFController extends Controller
{
    public function exportSaleDeliveryNote(Request $request, $id)
    {
        $businessId = (int) $request->query('business_id');
        if ($businessId <= 0) {
            return response()->json(['message' => 'business_id query parameter is required.'], 422);
        }

        $sale = Sale::with(['client', 'saleItems.product'])
            ->where('id', $id)
            ->where('department_id', $businessId)
            ->first();

        if (!$sale) {
            return response()->json(['message' => 'Sale not found for this business_id.'], 404);
        }

        $company = Company::first();
        $amountLetter = $this->convertAmoutToLetter((float) $sale->total_amount);

        $departmentColumns = ['name'];
        foreach (['address', 'phone', 'email', 'logo_url', 'logo'] as $optionalColumn) {
            if (Schema::hasColumn('departments', $optionalColumn)) {
                $departmentColumns[] = $optionalColumn;
            }
        }

        $department = DB::table('departments')
            ->select($departmentColumns)
            ->where('id', $businessId)
            ->first();

        $departmentName = $department->name ?? ($company->name ?? '');
        $departmentProfession = $department->profession ?? null;
        $departmentAddress = $department->address ?? ($company->address ?? '');
        $departmentPhone = $department->phone ?? ($company->phone ?? '');
        $departmentEmail = $department->email ?? ($company->email ?? '');

        $logoPath = $department->logo_url ?? ($department->logo ?? null);
        $logoAbsolutePath = null;
        if (!empty($logoPath)) {
            $cleanPath = ltrim((string) $logoPath, '/\\');
            $publicCandidate = public_path($cleanPath);
            $storageCandidate = storage_path('app/public/' . $cleanPath);
            if (file_exists($publicCandidate)) {
                $logoAbsolutePath = $publicCandidate;
            } elseif (file_exists($storageCandidate)) {
                $logoAbsolutePath = $storageCandidate;
            }
        }

        $pdf = Pdf::loadView('sale_delivery_pdf', compact(
            'sale',
            'amountLetter',
            'businessId',
            'departmentName',
            'departmentProfession',
            'departmentAddress',
            'departmentPhone',
            'departmentEmail',
            'logoAbsolutePath'
        ));

        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download('Bon_de_livraison_' . $sale->id . '.pdf');
    }

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
    public function exportSubCertifyInvoice($invoiceId)
    {
        $invoice = \App\Models\SubCertifyInvoices::with(['client', 'subCertifyInvoiceProducts.product'])->findOrFail($invoiceId);
        $company = Company::first();
        
        $totalTTC = $invoice->amount + ($invoice->tva_amount ?: ($invoice->amount * 0.19)) + ($invoice->timbre_amount ?: 0);
        $amountLetter = $this->convertAmoutToLetter($totalTTC);
        
        $pdf = Pdf::loadView('sub_certify_invoice_pdf', compact('invoice', 'company', 'amountLetter'));
        
        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
            
        return $pdf->stream('Facture_Sous_Traitant_' . $invoice->fac_id . '.pdf');
    }

    public function exportMultiCheques(Request $request)
    {
        $ids = $request->input('ids');
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return response()->json(['error' => 'No IDs provided'], 400);
        }

        $cheques = \App\Models\Cheque::with(['client'])->whereIn('id', $ids)->get();
        $company = Company::first();

        foreach ($cheques as $cheque) {
            $cheque->amountLetter = $this->convertAmoutToLetter($cheque->amount);
        }

        $pdf = Pdf::loadView('multi_cheques_pdf', compact('cheques', 'company'));

        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->stream('Cheques_Multiples.pdf');
    }

    public function generateCustomerLog()
    {
    }
}
