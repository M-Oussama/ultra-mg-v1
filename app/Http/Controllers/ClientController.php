<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Client;
use App\Models\Payment;
use App\Models\ProductReturn;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;
use Barryvdh\DomPDF\Facade\Pdf;

class ClientController extends Controller
{
    //

    /**
     * get List Of All Users
     *
     * @param Request $request
     * @return JsonResponse
     */

    #[OA\Get(
        path: "/api/clients/list",
        operationId: "getClients",
        description: "Returns the list of clients",
        tags: ["clients"],
    )]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/IClient",
        type: 'object'
    )])]
    public function getClients(Request $request): JsonResponse
    {

        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided
        $department_id = $request->input('department_id', '');


        $clients = Client::query();

        // Hierarchical Data Isolation
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                // Managers see all clients in their assigned departments
                $deptIds = $user->departments->pluck('id')->toArray();
                $clients->whereIn('department_id', $deptIds);
            } else {
                // Others (Salespeople) see only their own clients
                $clients->where('user_id', $user->id);
            }
        }

        $clients->with(['balance', 'sales','payments'])
            ->withSum(['sales as total_spent' => function ($query) use ($department_id) {
                if ($department_id) {
                    $query->where('department_id', $department_id);
                }
            }], 'total_amount')
            ->when($searchValue, function ($queryBuilder) use ($searchValue) {
                // Search for users with matching name or email
                $queryBuilder->where('name', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('surname', 'LIKE', '%' . $searchValue . '%');
            })->when($department_id, function ($queryBuilder) use ($department_id) {
                $queryBuilder->where('department_id', $department_id);
            });
        $clientsPage = $clients->paginate($perPage, ['*'], 'page', $currentPage);

        $totalUsers = $clientsPage->total(); // Total number of users matching the query
        $totalPage = ceil($totalUsers / $perPage); // Calculate total pages

        $cities = City::all();

        foreach ($clientsPage as $client){
            $this->calculateClientBalance($client);
        }

        return response()->json(["clients" => $clientsPage, "totalPage" => $totalPage, "totalClients"=>$totalUsers, 'cities'=>$cities]);
    }

    /**
     * Get all cities for city picker dropdown.
     */
    public function getCities(): JsonResponse
    {
        $cities = City::orderBy('name')->get();
        return response()->json(['cities' => $cities]);
    }

    /**
     * create a new user
     *
     * @param Request $request
     * @return JsonResponse
     */

    #[OA\Post(
        path: "/api/clients/store",
        operationId: "createClient",
        description: "Create a new Client",
        tags: ["clients"],
    )]
    #[OA\RequestBody(required: true, content: [new OA\JsonContent(
        required: ["name", "surname", "email", "address", "phone", "NRC", "NIS", "NART", "NIF"],
        properties: [
            new OA\Property(property: "name", type: "string", example: "john"),
            new OA\Property(property: "surname", type: "string", example: "john"),
            new OA\Property(property: "phone", type: "string", example: "0665461326"),
            new OA\Property(property: "address", type: "string", example: "address"),
            new OA\Property(property: "NRC", type: "string", example: "NRC"),
            new OA\Property(property: "NIS", type: "string", example: "NIS"),
            new OA\Property(property: "NART", type: "string", example: "NART"),
            new OA\Property(property: "NIF", type: "string", example: "NIF"),
            new OA\Property(property: "email", type: "string", example: "doe@gmail.com"),
        ]
    )])]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/IClient",
        type: 'object'
    )])]
    public function store(Request $request): JsonResponse
    {

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'city_id' => 'required|integer',
            'surname' => 'string|nullable|max:255',
            'phone' => 'string|nullable|max:255',
            'address' => 'string|nullable|max:255',
            'NRC' => 'string|nullable|max:255',
            'NIF' => 'string|nullable|max:255',
            'NART' => 'string|nullable|max:255',
            'NIS' => 'string|nullable|max:255',
            'email' => 'nullable|email|unique:users,email',
            'department_id' => 'nullable|integer|exists:departments,id',
            'brand' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'preferred_product_ids' => 'nullable|array',
        ]);

        $validatedData['department_id'] = $request->input('department_id', 1);
        $validatedData['user_id'] = \Illuminate\Support\Facades\Auth::id();

        // Create a new user record in the database using User::create()
        $client = Client::create($validatedData);

        $client->full_name = $client->surname ? $client->name.' '.$client->surname : $client->name;
        $client->save();

        //$this->calculateClientBalance($client);

        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'Client created successfully', 'client' => $client]);

    }

    #[OA\Post(
        path: "/api/clients/update/{id}",
        operationId: "updateClient",
        description: "update Client",
        tags: ["clients"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'number'))]

    #[OA\RequestBody(required: true, content: [new OA\JsonContent(
        required: ["name", "surname", "email", "address", "phone", "NRC", "NIS", "NART", "NIF"],
        properties: [
            new OA\Property(property: "name", type: "string", example: "john"),
            new OA\Property(property: "surname", type: "string", example: "john"),
            new OA\Property(property: "phone", type: "string", example: "0665461326"),
            new OA\Property(property: "address", type: "string", example: "address"),
            new OA\Property(property: "NRC", type: "string", example: "NRC"),
            new OA\Property(property: "NIS", type: "string", example: "NIS"),
            new OA\Property(property: "NART", type: "string", example: "NART"),
            new OA\Property(property: "NIF", type: "string", example: "NIF"),
            new OA\Property(property: "email", type: "string", example: "doe@gmail.com"),
        ]
    )])]

    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/IClient",
        type: 'object'
    )])]

    public function update(int $id,Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'surname' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'NRC' => 'nullable|string|max:255',
            'NIF' => 'nullable|string|max:255',
            'NART' => 'nullable|string|max:255',
            'NIS' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'department_id' => 'nullable|integer|exists:departments,id',
            'city_id' => 'nullable|integer',
            'brand' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'preferred_product_ids' => 'nullable|array',
        ]);

        if (!$request->filled('department_id')) {
            unset($validatedData['department_id']);
        }

        $client = Client::find($id);


        $client->update($validatedData);
        $client->full_name = $client->surname ? $client->name.' '.$client->surname : $client->name;
        $client->save();
        // Optionally, you can return a response, redirect the user, or perform any other actions here
        return response()->json(['message' => 'Client updated successfully', 'client' => $client]);

    }

    /**
     * Delete a client
     *
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Delete(
        path: "/api/clients/delete/{id}",
        operationId: "deleteClient",
        description: "delete a client",
        tags: ["users"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: 200,
        description: "Successfully deleted",
        content: new OA\JsonContent(type: "boolean", example: true)
    )]
    public function delete(int $id): JsonResponse
    {
        $client = Client::find($id);
        $client->delete();
        return response()->json(["message" => "User deleted successfully"]);
    }

    public function getClientsPerCity($cityId) {
        $query = Client::query();

        // Hierarchical Data Isolation
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && !$user->isGlobalAdmin()) {
            if ($user->isDepartmentManager()) {
                $deptIds = $user->departments->pluck('id')->toArray();
                $query->whereIn('department_id', $deptIds);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        if($cityId != "null") {
            $query->where('city_id',$cityId);
        }

        $clients = $query->get();
        return response()->json(['clients'=>$clients]);
    }

    public function exportClientLog($clientId){
        $client = Client::find($clientId);

        // Normalizing the date field and adding a type field
        $invoices = Sale::where('client_id', $clientId)
            ->get()
            ->map(function ($item) {
                $item->date = $item->sale_date; // Normalize to 'date'
                $item->type = 'Invoice';
                return $item;
            });

        $payments = Payment::where('client_id', $clientId)
            ->get()
            ->map(function ($item) {
                $item->date = $item->payment_date; // Normalize to 'date'
                $item->type = 'Payment';
                return $item;
            });

        $logEntries = collect([$invoices, $payments ])->flatten()->sortBy('date');


        $pdf = Pdf::loadView('client_log_pdf', compact('client', 'logEntries'));
        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
            ]);
        return $pdf->stream('client_log.pdf');
    }


    public function exportClientLogWithReturn($clientId){
        $client = Client::find($clientId);

        // Normalizing the date field and adding a type field
        $invoices = Sale::where('client_id', $clientId)
            ->get()
            ->map(function ($item) {
                $item->date = $item->sale_date; // Normalize to 'date'
                $item->type = 'Invoice';
                return $item;
            });

        $payments = Payment::where('client_id', $clientId)
            ->get()
            ->map(function ($item) {
                $item->date = $item->payment_date; // Normalize to 'date'
                $item->type = 'Payment';
                return $item;
            });
        $returns = ProductReturn::where('client_id', $clientId)->where('paid', false)
            ->get()
            ->map(function ($item) {
                $item->type = 'Return';
                return $item;
            });

        $logEntries = collect([$invoices,$payments,  $returns])->flatten()->sortBy('date');


        $pdf = Pdf::loadView('client_log_return_pdf', compact('client', 'logEntries'));
        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
            ]);
        return $pdf->stream('ETAT '.$client->name.'.pdf');
    }

    public function exportClientProductLog($clientId){
        $client = Client::find($clientId);

        // Normalizing the date field and adding a type field
        $invoices = SaleItem::with('product')->orderBy('sale_date', 'desc')->where('client_id', $clientId)
            ->get();



        $pdf = Pdf::loadView('products_log', compact('client', 'invoices'));
        $pdf->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isFontSubsettingEnabled' => true,
            ]);
        return $pdf->stream('ETAT '.$client->name.'.pdf');
    }

    /**
     * Import clients from CSV.
     *
     * Expected CSV headers:
     * id, name, surname, full_name, address, email, phone, NRC, NIF, NART, NIS, city_id
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

        foreach (['id', 'name', 'surname', 'address', 'email', 'phone', 'nrc', 'nif', 'nart', 'nis', 'city_id'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

        $userId = auth()->id();
        $defaultDepartmentId = (int) ($request->input('department_id', 1));

        $inserted = 0;
        $errors = [];
        $warnings = [];
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

            $name = $rowData['name'] ?? null;
            $surname = $rowData['surname'] ?? null;
            $fullName = trim(($name ?? '') . ' ' . ($surname ?? ''));
            $rawEmail = trim((string) ($rowData['email'] ?? ''));
            $email = filter_var($rawEmail, FILTER_VALIDATE_EMAIL) ? $rawEmail : null;

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'department_id' => $defaultDepartmentId,
                'name' => $name,
                'surname' => $surname,
                'full_name' => $fullName !== '' ? $fullName : null,
                'address' => $rowData['address'] ?? null,
                'email' => $email,
                'phone' => $rowData['phone'] ?? null,
                'NRC' => $rowData['nrc'] ?? null,
                'NIF' => $rowData['nif'] ?? null,
                'NART' => $rowData['nart'] ?? null,
                'NIS' => $rowData['nis'] ?? null,
                'city_id' => isset($rowData['city_id']) ? (int) $rowData['city_id'] : null,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'department_id' => 'nullable|integer',
                'name' => 'required|string|max:255',
                'surname' => 'nullable|string|max:255',
                'full_name' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'email' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'NRC' => 'nullable|string|max:255',
                'NIF' => 'nullable|string|max:255',
                'NART' => 'nullable|string|max:255',
                'NIS' => 'nullable|string|max:255',
                'city_id' => 'required|integer',
                'user_id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            if (!empty($payload['id']) && DB::table('clients')->where('id', $payload['id'])->exists()) {
                $warnings[] = [
                    'row' => $rowNumber,
                    'warning' => 'Client id exists, inserted with auto-generated id.',
                ];
                unset($payload['id']);
            }

            DB::table('clients')->insert($payload);
            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import processed',
            'inserted' => $inserted,
            'failed' => count($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ]);
    }
}
