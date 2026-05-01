<?php

namespace App\Http\Controllers;

use App\Models\ImportationInvoice;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get overall statistics for the dashboard.
     */
    public function statistics()
    {
        $totalInvoices = ImportationInvoice::count();
        $totalSuppliers = Supplier::count();
        $totalAmount = ImportationInvoice::sum('amount');
        
        // In a real scenario, paid/unpaid would be calculated from payments.
        // For now, providing a structured response that the Flutter app expects.
        $paidAmount = DB::table('importation_payments')->sum('amount');
        $unpaidAmount = $totalAmount - $paidAmount;

        return response()->json([
            'total_invoices' => $totalInvoices,
            'total_suppliers' => $totalSuppliers,
            'total_amount' => (float)$totalAmount,
            'paid_amount' => (float)$paidAmount,
            'unpaid_amount' => (float)$unpaidAmount,
            'paid_count' => DB::table('importation_payments')->distinct('importation_invoice_id')->count(),
            'unpaid_count' => $totalInvoices - DB::table('importation_payments')->distinct('importation_invoice_id')->count(),
        ]);
    }

    /**
     * Get monthly payable summary.
     */
    public function monthlyPayable(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = ImportationInvoice::query();
        if ($startDate) $query->where('start_date', '>=', $startDate);
        if ($endDate) $query->where('arrive_date', '<=', $endDate);

        $totalAmount = $query->sum('amount');
        $totalCount = $query->count();

        return response()->json([
            'total_amount' => (float)$totalAmount,
            'paid_amount' => 0.0, // Needs calculation logic
            'unpaid_amount' => (float)$totalAmount,
            'total_count' => $totalCount,
            'paid_count' => 0,
            'unpaid_count' => $totalCount,
        ]);
    }
}
