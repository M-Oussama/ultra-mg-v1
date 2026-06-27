<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\CertifyClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class CertifyClientController extends Controller
{
    /**
     * get List Of All Certify Clients
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(
        path: "/api/certify-clients/list",
        operationId: "getCertifyClients",
        description: "Returns the list of certify clients",
        tags: ["certify-clients"],
    )]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/ICertifyClient",
        type: 'object'
    )])]
    public function getClients(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', '');
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $subCertifyOnly = filter_var(
            $request->input('sub_certify_only', false),
            FILTER_VALIDATE_BOOLEAN
        );

        $clientsQuery = CertifyClient::with(['city', 'media'])
            ->when($subCertifyOnly, function ($queryBuilder) {
                $queryBuilder->where('is_sub_certify', true);
            })
            ->when($searchValue, function ($queryBuilder) use ($searchValue) {
                $queryBuilder->where(function ($query) use ($searchValue) {
                    $query->where('name', 'LIKE', '%' . $searchValue . '%')
                        ->orWhere('surname', 'LIKE', '%' . $searchValue . '%');
                });
            })
            ->orderBy('name')
            ->orderBy('surname');

        $clientsAll = (clone $clientsQuery)->get();
        $clientsPage = $clientsQuery->paginate($perPage, ['*'], 'page', $currentPage);

        $totalClients = $clientsPage->total();
        $totalPage = ceil($totalClients / $perPage);

        $cities = City::orderBy('name')->get();

        return response()->json([
            "clients" => $clientsPage,
            'clientsAll' => $clientsAll,
            "totalPage" => $totalPage,
            "totalClients" => $totalClients,
            'cities' => $cities
        ]);
    }

    /**
     * Get all cities for certify client dropdowns.
     */
    #[OA\Get(
        path: "/api/certify-clients/cities",
        operationId: "getCertifyClientCities",
        description: "Returns the list of cities for certify clients",
        tags: ["certify-clients"],
    )]
    #[OA\Response(response: 200, description: "Success")]
    public function getCities(): JsonResponse
    {
        return response()->json([
            'cities' => City::orderBy('name')->get(),
        ]);
    }

    /**
     * create a new certify client
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/certify-clients/store",
        operationId: "createCertifyClient",
        description: "Create a new Certify Client",
        tags: ["certify-clients"],
    )]
    #[OA\RequestBody(required: true, content: [new OA\JsonContent(
        required: ["name", "surname", "city_id"],
        properties: [
            new OA\Property(property: "name", type: "string", example: "john"),
            new OA\Property(property: "surname", type: "string", example: "john"),
            new OA\Property(property: "profession", type: "string", example: "Trader"),
            new OA\Property(property: "city_id", type: "integer", example: 1),
            new OA\Property(property: "phone", type: "string", example: "0665461326"),
            new OA\Property(property: "address", type: "string", example: "address"),
            new OA\Property(property: "NRC", type: "string", example: "NRC"),
            new OA\Property(property: "is_cnrc_active", type: "boolean", example: true),
            new OA\Property(property: "NIS", type: "string", example: "NIS"),
            new OA\Property(property: "NART", type: "string", example: "NART"),
            new OA\Property(property: "NIF", type: "string", example: "NIF"),
            new OA\Property(property: "is_nif_active", type: "boolean", example: true),
            new OA\Property(property: "is_sub_certify", type: "boolean", example: false),
            new OA\Property(property: "email", type: "string", example: "doe@gmail.com"),
        ]
    )])]
    #[OA\Response(response:200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/ICertifyClient",
        type: 'object'
    )])]
    public function store(Request $request): JsonResponse
    {
        $payload = $this->normalizeCertifyClientPayload($request);
        $validator = Validator::make($payload, [
            'name' => 'required|string|max:255',
            'city_id' => 'required|integer',
            'surname' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'NRC' => 'nullable|string|max:255',
            'is_cnrc_active' => 'nullable|boolean',
            'NIF' => 'nullable|string|max:255',
            'is_nif_active' => 'nullable|boolean',
            'is_sub_certify' => 'nullable|boolean',
            'NART' => 'nullable|string|max:255',
            'NIS' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'nif_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff,heic,heif|max:10240',
            'cnrc_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff,heic,heif|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $client = CertifyClient::create($validator->validated());
        $client->full_name = $client->surname ? $client->name . ' ' . $client->surname : $client->name;
        $client->save();

        $this->syncCertifyClientMedia($client, $request);

        return response()->json([
            'message' => 'Certify Client created successfully',
            'client' => $client->fresh(['city']),
        ]);
    }

    /**
     * update a certify client
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/certify-clients/update/{id}",
        operationId: "updateCertifyClient",
        description: "update Certify Client",
        tags: ["certify-clients"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: [new OA\JsonContent(
        properties: [
            new OA\Property(property: "name", type: "string", example: "john"),
            new OA\Property(property: "surname", type: "string", example: "john"),
            new OA\Property(property: "profession", type: "string", example: "Trader"),
            new OA\Property(property: "phone", type: "string", example: "0665461326"),
            new OA\Property(property: "address", type: "string", example: "address"),
            new OA\Property(property: "NRC", type: "string", example: "NRC"),
            new OA\Property(property: "is_cnrc_active", type: "boolean", example: true),
            new OA\Property(property: "NIS", type: "string", example: "NIS"),
            new OA\Property(property: "NART", type: "string", example: "NART"),
            new OA\Property(property: "NIF", type: "string", example: "NIF"),
            new OA\Property(property: "is_nif_active", type: "boolean", example: true),
            new OA\Property(property: "is_sub_certify", type: "boolean", example: false),
            new OA\Property(property: "email", type: "string", example: "doe@gmail.com"),
        ]
    )])]
    #[OA\Response(response:200, description: "Success")]
    public function update(int $id, Request $request): JsonResponse
    {
        $client = CertifyClient::findOrFail($id);
        $payload = $this->normalizeCertifyClientPayload($request);
        $validator = Validator::make($payload, [
            'name' => 'nullable|string|max:255',
            'surname' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city_id' => 'nullable|integer',
            'NRC' => 'nullable|string|max:255',
            'is_cnrc_active' => 'nullable|boolean',
            'NIF' => 'nullable|string|max:255',
            'is_nif_active' => 'nullable|boolean',
            'is_sub_certify' => 'nullable|boolean',
            'NART' => 'nullable|string|max:255',
            'NIS' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'nif_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff,heic,heif|max:10240',
            'cnrc_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp,tif,tiff,heic,heif|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $client->update($validatedData);
        $client->full_name = $client->surname ? $client->name . ' ' . $client->surname : $client->name;
        $client->save();

        $this->syncCertifyClientMedia($client, $request);

        return response()->json([
            'message' => 'Certify Client updated successfully',
            'client' => $client->fresh(['city']),
        ]);
    }

    private function normalizeCertifyClientPayload(Request $request): array
    {
        $payload = $request->all();

        $payload['NRC'] = $this->requestFieldValue($request, 'NRC', 'nrc');
        $payload['NIF'] = $this->requestFieldValue($request, 'NIF', 'nif');
        $payload['NART'] = $this->requestFieldValue($request, 'NART', 'nart');
        $payload['NIS'] = $this->requestFieldValue($request, 'NIS', 'nis');
        if ($request->has('is_sub_certify') || $request->has('isSubCertify')) {
            $payload['is_sub_certify'] = $this->requestFieldValue(
                $request,
                'is_sub_certify',
                'isSubCertify'
            );
        }

        return $payload;
    }

    private function requestFieldValue(Request $request, string $primaryField, string $fallbackField)
    {
        $primaryValue = $request->input($primaryField);
        if ($primaryValue !== null && $primaryValue !== '') {
            return $primaryValue;
        }

        return $request->input($fallbackField);
    }

    private function syncCertifyClientMedia(CertifyClient $client, Request $request): void
    {
        if ($request->hasFile('pdf_file')) {
            $client->clearMediaCollection('pdf_file');
            $client->addMediaFromRequest('pdf_file')->toMediaCollection('pdf_file');
        }

        if ($client->is_nif_active) {
            if ($request->hasFile('nif_file')) {
                $client->clearMediaCollection('nif_file');
                $client->addMediaFromRequest('nif_file')->toMediaCollection('nif_file');
            }
        } else {
            $client->clearMediaCollection('nif_file');
        }

        if ($client->is_cnrc_active) {
            if ($request->hasFile('cnrc_file')) {
                $client->clearMediaCollection('cnrc_file');
                $client->addMediaFromRequest('cnrc_file')->toMediaCollection('cnrc_file');
            }
        } else {
            $client->clearMediaCollection('cnrc_file');
        }
    }

    /**
     * Delete a certify client
     *
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Delete(
        path: "/api/certify-clients/delete/{id}",
        operationId: "deleteCertifyClient",
        description: "delete a certify client",
        tags: ["certify-clients"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: "Successfully deleted")]
    public function delete(int $id): JsonResponse
    {
        $client = CertifyClient::findOrFail($id);
        $client->delete();
        return response()->json(["message" => "Certify Client deleted successfully"]);
    }

    /**
     * Import certify clients from CSV.
     *
     * Accepted CSV headers:
     * name (required), surname, profession, address, phone, NRC, is_cnrc_active,
     * NIF, is_nif_active, is_sub_certify, NART, NIS, email, city_id or city
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

        $normalizedHeader = array_map(function ($col) {
            return strtolower(trim((string) $col));
        }, $header);

        $requiredColumns = ['name'];
        foreach ($requiredColumns as $column) {
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

            $cityId = null;
            if (!empty($rowData['city_id'])) {
                $cityId = (int) $rowData['city_id'];
            } elseif (!empty($rowData['city'])) {
                $city = City::whereRaw('LOWER(name) = ?', [strtolower($rowData['city'])])->first();
                $cityId = $city?->id;
            }

            if ($cityId === null) {
                $cityId = 0;
            }

            $payload = [
                'name' => $rowData['name'] ?? null,
                'surname' => $rowData['surname'] ?? null,
                'profession' => $rowData['profession'] ?? null,
                'address' => $rowData['address'] ?? null,
                'phone' => $rowData['phone'] ?? null,
                'NRC' => $rowData['nrc'] ?? null,
                'is_cnrc_active' => filter_var($rowData['is_cnrc_active'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                'NIF' => $rowData['nif'] ?? null,
                'is_nif_active' => filter_var($rowData['is_nif_active'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                'is_sub_certify' => filter_var($rowData['is_sub_certify'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                'NART' => $rowData['nart'] ?? null,
                'NIS' => $rowData['nis'] ?? null,
                'email' => $rowData['email'] ?? null,
                'city_id' => $cityId,
            ];

            $validator = Validator::make($payload, [
                'name' => 'required|string|max:255',
                'surname' => 'nullable|string|max:255',
                'profession' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'NRC' => 'nullable|string|max:255',
                'is_cnrc_active' => 'nullable|boolean',
                'NIF' => 'nullable|string|max:255',
                'is_nif_active' => 'nullable|boolean',
                'is_sub_certify' => 'nullable|boolean',
                'NART' => 'nullable|string|max:255',
                'NIS' => 'nullable|string|max:255',
                'email' => 'nullable|email',
                'city_id' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            $client = CertifyClient::create($validator->validated());
            $client->full_name = $client->surname ? ($client->name . ' ' . $client->surname) : $client->name;
            $client->save();
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
