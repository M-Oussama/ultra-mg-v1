<?php

namespace App\Http\Controllers;

use App\Models\SubCertifyInvoiceProducts;
use App\Models\SubCertifyInvoices;
use App\Models\CertifyClient;
use App\Models\CertifyProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class SubCertifyInvoiceController extends Controller
{
    /**
     * get List Of All Sub Invoices
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(
        path: "/api/sub-certify-invoices/list",
        operationId: "getSubInvoices",
        description: "Returns the list of sub invoices",
        tags: ["subCertifyInvoice"],
    )]
    public function getInvoices(Request $request): JsonResponse
    {
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);

        $invoices = SubCertifyInvoices::orderBy('date', 'desc')->paginate($perPage, ['*'], 'page', $currentPage);
        $totalInvoices = $invoices->total();
        $totalPage = ceil($totalInvoices / $perPage);

        return response()->json([
            "invoices" => $invoices,
            "totalPage" => $totalPage,
            "totalInvoices" => $totalInvoices
        ]);
    }

    public function getInvoice($id): JsonResponse
    {
        $invoice = SubCertifyInvoices::find($id);
        $clients = CertifyClient::all();
        $products = CertifyProduct::getAllProductsFormatted();

        return response()->json([
            "invoice" => $invoice,
            "clients" => $clients,
            "products" => $products,
            "unSelectedProducts" => $products
        ]);
    }

    /**
     * Create a new Sub Certify Invoice
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $invoiceData = $request->input('invoiceData');
        $client = $invoiceData['client'];
        $fac_id = $invoiceData['fac_id'];

        $invoice = SubCertifyInvoices::create([
            'certify_invoice_id' => $invoiceData['certify_invoice_id'],
            'fac_id' => $fac_id,
            'date' => $invoiceData['date'],
            'client_id' => $client['id'],
            'amount' => $invoiceData['total'] ?? $invoiceData['amount'] ?? 0,
            'payment_type' => $invoiceData['payment_type'],
            'tva_rate' => $invoiceData['tva_rate'] ?? null,
            'tva_amount' => $invoiceData['tva_amount'] ?? null,
            'ht_amount' => $invoiceData['ht_amount'] ?? null,
            'timbre_rate' => $invoiceData['timbre_rate'] ?? null,
            'timbre_amount' => $invoiceData['timbre_amount'] ?? null,
            'cheque_number' => $invoiceData['cheque_number'] ?? null,
            'cheque_id' => $invoiceData['cheque_id'] ?? null,
        ]);

        $products = $invoiceData['certify_invoice_products'];

        foreach ($products as $product) {
            SubCertifyInvoiceProducts::create([
                'product_id' => $product['product']['id'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'total' => $product['quantity'] * $product['price'],
                'sub_certify_invoice_id' => $invoice->id,
            ]);
        }

        return response()->json(['message' => 'Sub Invoice created successfully', "id" => $invoice->id]);
    }

    public function getInvoiceData(): JsonResponse
    {
        $clients = CertifyClient::all();
        $products = CertifyProduct::getAllProductsFormatted();
        $date = date('Y-m-d');
        $id = $this->getLastIDPerYear(date('Y-m-d'));

        return response()->json(["clients" => $clients, "products" => $products, "id" => $id, "date" => $date]);
    }

    public function getLastID(Request $request)
    {
        $date = $request->input('date');
        $last_id = $this->getLastIDPerYear($date);
        return response()->json(["id" => $last_id]);
    }

    public function getLastIDPerYear($date)
    {
        $year = date("Y", strtotime($date));
        $max_fac_id = SubCertifyInvoices::whereYear('date', $year)->max('fac_id');

        return ($max_fac_id ?? 0) + 1;
    }

    public function update(Request $request): JsonResponse
    {
        $invoiceData = $request->input('invoiceData');
        $client = $invoiceData['client'];

        $invoice = SubCertifyInvoices::find($invoiceData['id']);

        $invoice->update([
            'certify_invoice_id' => $invoiceData['certify_invoice_id'],
            'date' => $invoiceData['date'],
            'client_id' => $client['id'],
            'amount' => $invoiceData['total'] ?? $invoiceData['amount'] ?? 0,
            'payment_type' => $invoiceData['payment_type'],
            'tva_rate' => $invoiceData['tva_rate'] ?? null,
            'tva_amount' => $invoiceData['tva_amount'] ?? null,
            'ht_amount' => $invoiceData['ht_amount'] ?? null,
            'timbre_rate' => $invoiceData['timbre_rate'] ?? null,
            'timbre_amount' => $invoiceData['timbre_amount'] ?? null,
            'cheque_number' => $invoiceData['cheque_number'] ?? null,
            'cheque_id' => $invoiceData['cheque_id'] ?? null,
        ]);

        $invoice->subCertifyInvoiceProducts()->delete();

        $products = $invoiceData['certify_invoice_products'];

        foreach ($products as $product) {
            SubCertifyInvoiceProducts::create([
                'product_id' => $product['product']['id'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'total' => $product['price'] * $product['quantity'],
                'sub_certify_invoice_id' => $invoice->id,
            ]);
        }

        return response()->json(["message" => "Sub Invoice Updated Successfully"]);
    }

    /**
     * Delete a sub certify invoice
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $invoice = SubCertifyInvoices::findOrFail($id);
        $invoice->subCertifyInvoiceProducts()->delete();
        $invoice->delete();

        return response()->json(["message" => "Sub Certify Invoice deleted successfully"]);
    }
}
