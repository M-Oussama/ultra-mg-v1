<?php

namespace App\Http\Controllers;

use App\Http\Helpers\NumberToLetter;
use App\Models\CertifyInvoiceProducts;
use App\Models\CertifyInvoices;
use App\Models\CertifyClient;
use App\Models\Payment;
use App\Models\CertifyProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class CertifyInvoiceController extends Controller
{

    /**
     * get List Of All Invoices
     *
     * @param Request $request
     * @return JsonResponse
     */

    #[OA\Get(
        path: "/api/certifyInvoices/list",
        operationId: "getInvoices",
        description: "Returns the list of invoices",
        tags: ["certifyInvoice"],
    )]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/ICertifyInvoice",
        type: 'object'
    )])]
    public function getInvoices(Request $request): JsonResponse
    {
        $searchValue = trim((string) $request->input('searchValue', ''));
        $clientId = $request->input('client_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $modifiedOnly = filter_var($request->input('modified_only', false), FILTER_VALIDATE_BOOLEAN);
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided
        $invoices = CertifyInvoices::orderBy('date', 'desc');

        if ($searchValue !== '') {
            $invoices->where(function ($query) use ($searchValue) {
                $query->where('fac_id', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('payment_type', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('amount', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('custom_cheque_number', 'LIKE', '%' . $searchValue . '%')
                    ->orWhereHas('client', function ($clientQuery) use ($searchValue) {
                        $clientQuery->where('name', 'LIKE', '%' . $searchValue . '%')
                            ->orWhere('surname', 'LIKE', '%' . $searchValue . '%')
                            ->orWhereRaw(
                                "CONCAT(COALESCE(name, ''), ' ', COALESCE(surname, '')) LIKE ?",
                                ['%' . $searchValue . '%']
                            );
                    });
            });
        }

        if (!empty($clientId) && $clientId !== 'all') {
            $invoices->where('client_id', $clientId);
        }

        if (!empty($startDate)) {
            $invoices->whereDate('date', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $invoices->whereDate('date', '<=', $endDate);
        }

        if ($modifiedOnly) {
            $invoices->whereColumn('updated_at', '>', 'created_at');
        }

        // Hierarchical Data Isolation
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                // Managers see all invoices in their assigned departments (via client)
                $deptIds = $user->departments->pluck('id')->toArray();
                $invoices->whereHas('client', function($q) use ($deptIds) {
                    $q->whereIn('department_id', $deptIds);
                });
            } else {
                // Others (Salespeople) see only their own invoices
                $invoices->where('user_id', $user->id);
            }
        }

        $invoices = $invoices->paginate($perPage, ['*'], 'page', $currentPage);
        $totalInvoices = $invoices->total(); // Total number of invoices matching the query
        $totalPage = ceil($totalInvoices / $perPage); // Calculate total pages

        return response()->json(["invoices" => $invoices, "totalPage" => $totalPage, "totalInvoices"=>$totalInvoices]);
    }

    public function getInvoice($id): JsonResponse
    {
        $invoice = CertifyInvoices::find($id);

        $invoice->amount_letter = $this->convertAmoutToLetter(($invoice->amount*1.19));

        $clients = CertifyClient::all();

        $products = CertifyProduct::getAllProductsFormatted();

        return response()->json(["invoice" => $invoice, "clients"=>$clients, "products"=>$products, "unSelectedProducts"=>$products]);
    }

    /**
     * Create a new Certify Invoice
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/certifyInvoices/store",
        operationId: "store",
        description: "Create a new Certify Invoice",
        tags: ["certifyInvoice"],
    )]
    #[OA\RequestBody(
        required: true,
        content: [
            new OA\JsonContent(
                required: ["client_id", "amount", "date"],
                properties: [
                    new OA\Property(property: "client_id", type: "integer", example: "1"),
                    new OA\Property(property: "amount", type: "number", format: "float", example: "123"),
                    new OA\Property(property: "date",  type: "string", format: "date-time", example: "2023-08-13"),
                    new OA\Property(property: "payment_type", type: "integer", example: "1"),
                    new OA\Property(property: "products", type: "array",
                        items: new OA\Items(
                            properties: [  // Example object of type object
                            new OA\Property(property: "product_id", type: "integer", example: "1"),
                            new OA\Property(property: "quantity", type: "number", example: "2"),
                            new OA\Property(property: "price", type: "number", example: "2"),
                            new OA\Property(property: "total", type: "number", example: "2"),
                            ],

                        )),

                ]
            )
        ]
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: [
            new OA\JsonContent(
                ref: "#/components/schemas/ICertifyInvoice",
                type: 'object'
            )
        ]
    )]

    public function store(Request $request): JsonResponse
    {

        $invoiceData = $request->input('invoiceData');
        $client = $invoiceData['client'];

        $fac_id = $invoiceData['fac_id'];

         $invoice = CertifyInvoices::create([
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
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        $products = $invoiceData['certify_invoice_products'];

        foreach ($products as $product) {

            CertifyInvoiceProducts::create([
               'product_id' => $product['product']['id'],
               'price' => $product['price'],
               'quantity' => $product['quantity'],
               'total' => $product['quantity'] * $product['price'],
               'certify_invoice_id' => $invoice->id,
            ]);
        }



        return response()->json(['message' => 'Product created successfully', "id"=>$invoice->id]);

    }

    /**
     * get List Of Clients and Products
     *
     * @return JsonResponse
     */

    #[OA\Get(
        path: "/api/certifyInvoices/getInvoiceData",
        operationId: "getInvoiceData",
        description: "Returns the list of clients and products",
        tags: ["certifyInvoice"],
    )]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        type: 'object'
    )])]
    public function getInvoiceData(): JsonResponse {
        $clients = CertifyClient::all();
        $products = CertifyProduct::getAllProductsFormatted();
        $date = date('Y-m-d');
        $id = $this->getLastIDPerYear(date('Y-m-d'));

        return response()->json(["clients" => $clients, "products" => $products, "id"=>$id, "date"=>$date]);
    }

    public function getLastID(Request $request){
        $date = $request->input('date');

        $last_id = $this->getLastIDPerYear($date);

        return response()->json(["id" => $last_id]);
    }

    public function getLastIDPerYear($date){

        $year = date("Y",strtotime($date));

        $max_fac_id = CertifyInvoices::whereYear('date',$year)->max('fac_id');

        return ($max_fac_id ?? 0) + 1;
    }



    public function update(Request $request): JsonResponse
    {
        $invoiceData = $request->input('invoiceData');

        $client = $invoiceData['client'];

       $fac_id = $invoiceData['fac_id'];

       $invoice = CertifyInvoices::find($invoiceData['id']);

         $invoice->update([
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


        foreach ($invoice->certifyInvoiceProducts as $product) {
            $product->delete();
        }

        $products = $invoiceData['certify_invoice_products'];

         foreach ($products as $product) {

             CertifyInvoiceProducts::create([
                 'product_id' => $product['product']['id'],
                 'price' => $product['price'],
                 'quantity' => $product['quantity'],
                 'total' => $product['price'] * $product['quantity'],
                 'certify_invoice_id' => $invoiceData['id'],
             ]);
         }

        return response()->json(["message"=>"Invoice Updated Successfully"]);

    }

    /**
     * Delete a certify invoice
     *
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Delete(
        path: "/api/certifyInvoices/delete/{id}",
        operationId: "deleteCertifyInvoice",
        description: "Delete a certify invoice",
        tags: ["certifyInvoice"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: "Successfully deleted")]
    public function delete(int $id): JsonResponse
    {
        $invoice = CertifyInvoices::findOrFail($id);
        
        // Delete associated products
        $invoice->certifyInvoiceProducts()->delete();
        
        $invoice->delete();
        
        return response()->json(["message" => "Certify Invoice deleted successfully"]);
    }

    /**
     * Import certify invoices from CSV using fiscal mapping rules.
     *
     * Required headers:
     * id, fac_id, date, client_id, amount, custom_cheque_number, cheque_id, payment_type
     */
    public function importCsv(Request $request): JsonResponse
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

        foreach (['id', 'fac_id', 'date', 'client_id', 'amount', 'custom_cheque_number', 'cheque_id', 'payment_type'] as $column) {
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
            $clientId = isset($rowData['client_id']) ? (int) $rowData['client_id'] : null;
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
                'user_id' => auth()->id(),
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

            if (DB::table('certify_invoices')->where('id', $payload['id'])->exists()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => ['Invoice id already exists in database.'],
                ];
                continue;
            }

            DB::table('certify_invoices')->insert($payload);
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
     * Import certify invoice products from CSV.
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
                'certify_invoice_id' => isset($rowData['command_id']) ? (int) $rowData['command_id'] : null,
                'product_id' => isset($rowData['product_id']) ? (int) $rowData['product_id'] : null,
                'price' => isset($rowData['price']) ? (float) $rowData['price'] : null,
                'quantity' => isset($rowData['quantity']) ? (int) $rowData['quantity'] : null,
                'total' => isset($rowData['amount']) ? (float) $rowData['amount'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'certify_invoice_id' => 'required|integer|exists:certify_invoices,id',
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

            if (DB::table('certify_invoice_products')->where('id', $payload['id'])->exists()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => ['Certify invoice product id already exists in database.'],
                ];
                continue;
            }

            DB::table('certify_invoice_products')->insert($payload);
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

}
