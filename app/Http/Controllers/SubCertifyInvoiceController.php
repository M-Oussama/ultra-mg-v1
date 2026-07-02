<?php

namespace App\Http\Controllers;

use App\Models\SubCertifyInvoiceProducts;
use App\Models\SubCertifyInvoices;
use App\Models\CertifyClient;
use App\Models\CertifyProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
        $clients = CertifyClient::where('is_sub_certify', true)
            ->orderBy('name')
            ->orderBy('surname')
            ->get();
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
        $parentInvoiceId = data_get($invoiceData, 'certify_invoice_id')
            ?? data_get($invoiceData, 'certifyInvoiceId');

        if ($parentInvoiceId === null || $parentInvoiceId === '') {
            return response()->json([
                'message' => 'Missing certify_invoice_id.',
            ], 422);
        }

        $invoice = SubCertifyInvoices::create([
            'certify_invoice_id' => $parentInvoiceId,
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
        $clients = CertifyClient::where('is_sub_certify', true)
            ->orderBy('name')
            ->orderBy('surname')
            ->get();
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

        $invoice = SubCertifyInvoices::findOrFail(data_get($invoiceData, 'id'));
        $parentInvoiceId = data_get($invoiceData, 'certify_invoice_id')
            ?? data_get($invoiceData, 'certifyInvoiceId')
            ?? $invoice->certify_invoice_id;

        $invoice->update([
            'certify_invoice_id' => $parentInvoiceId,
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
     * Import sub certify invoices from CSV.
     *
     * The client_id is intentionally provided by the request, not the CSV.
     * Required CSV columns:
     * id, fac_id, date, amount, custom_cheque_number, cheque_id, payment_type
     */
    public function importCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'client_id' => 'required|integer',
        ]);

        $clientId = (int) $request->input('client_id');

        if (!CertifyClient::where('id', $clientId)->exists()) {
            return response()->json(['message' => 'Invalid client ID.'], 422);
        }

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);

        foreach (['id', 'fac_id', 'date', 'amount', 'custom_cheque_number', 'cheque_id', 'payment_type'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $baseAmount = isset($rowData['amount']) ? (float) $rowData['amount'] : 0;
            $paymentType = isset($rowData['payment_type']) ? (int) $rowData['payment_type'] : null;
            $tvaAmount = round($baseAmount * 0.19, 2);
            $rawChequeNumber = trim((string) ($rowData['custom_cheque_number'] ?? ''));
            $chequeNumber = ($rawChequeNumber === '' || strtolower($rawChequeNumber) === 'null')
                ? null
                : $rawChequeNumber;
            $rawChequeId = trim((string) ($rowData['cheque_id'] ?? ''));
            $chequeId = ($rawChequeId === '' || strtolower($rawChequeId) === 'null')
                ? null
                : (int) $rawChequeId;

            if ($paymentType === 1) {
                $timbreRate = 2;
                $timbreAmount = round($baseAmount * 0.02, 2);
                $finalAmount = round($baseAmount * 1.21, 2);
            } else {
                $timbreRate = null;
                $timbreAmount = null;
                $finalAmount = round($baseAmount * 1.19, 2);
            }

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'certify_invoice_id' => 1,
                'fac_id' => isset($rowData['fac_id']) ? (int) $rowData['fac_id'] : null,
                'date' => $rowData['date'] ?? null,
                'client_id' => $clientId,
                'ht_amount' => $baseAmount,
                'tva_amount' => $tvaAmount,
                'cheque_number' => $chequeNumber,
                'cheque_id' => $chequeId,
                'payment_type' => $paymentType,
                'timbre_rate' => $timbreRate,
                'timbre_amount' => $timbreAmount,
                'amount' => $finalAmount,
                'tva_rate' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'fac_id' => 'required|integer|min:0',
                'date' => 'required|date',
                'client_id' => 'required|integer|exists:certify_clients,id',
                'ht_amount' => 'required|numeric|min:0',
                'tva_amount' => 'required|numeric|min:0',
                'cheque_number' => 'nullable|string|max:255',
                'cheque_id' => 'nullable|integer|min:1',
                'payment_type' => 'required|integer|in:1,2,3,4',
                'timbre_rate' => 'nullable|numeric|min:0',
                'timbre_amount' => 'nullable|numeric|min:0',
                'amount' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];

                continue;
            }

            if (DB::table('sub_certify_invoices')->where('id', $payload['id'])->exists()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => ['Sub certify invoice id already exists in database.'],
                ];

                continue;
            }

            DB::table('sub_certify_invoices')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
        ]);
    }

    /**
     * Import sub certify invoice products from CSV.
     *
     * Required headers:
     * id, command_id, product_id, price, quantity, amount
     */
    public function importProductsCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return response()->json(['message' => 'Unable to read CSV file'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'CSV file is empty'], 422);
        }

        $normalizedHeader = array_map(fn($col) => strtolower(trim((string) $col)), $header);

        foreach (['id', 'command_id', 'product_id', 'price', 'quantity', 'amount'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $inserted = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $rowData = [];
            foreach ($normalizedHeader as $index => $columnName) {
                $rowData[$columnName] = isset($row[$index]) ? trim((string) $row[$index]) : null;
            }

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'sub_certify_invoice_id' => isset($rowData['command_id']) ? (int) $rowData['command_id'] : null,
                'product_id' => isset($rowData['product_id']) ? (int) $rowData['product_id'] : null,
                'price' => isset($rowData['price']) ? (float) $rowData['price'] : null,
                'quantity' => isset($rowData['quantity']) ? (int) $rowData['quantity'] : null,
                'total' => isset($rowData['amount']) ? (float) $rowData['amount'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'sub_certify_invoice_id' => 'required|integer|exists:sub_certify_invoices,id',
                'product_id' => 'required|integer|exists:certify_products,id',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'total' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];

                continue;
            }

            if (DB::table('sub_certify_invoice_products')->where('id', $payload['id'])->exists()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => ['Sub certify invoice product id already exists in database.'],
                ];

                continue;
            }

            DB::table('sub_certify_invoice_products')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
        ]);
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
