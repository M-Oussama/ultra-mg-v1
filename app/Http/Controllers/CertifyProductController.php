<?php

namespace App\Http\Controllers;

use App\Models\CertifyProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class CertifyProductController extends Controller
{
    /**
     * get List Of All Certify Products
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(
        path: "/api/certify-products/list",
        operationId: "getCertifyProducts",
        description: "Returns the list of certify products",
        tags: ["certify-products"],
    )]
    #[OA\Parameter(
        name: "searchValue",
        in: "query",
        required: false,
        description: "Search term for product name or brand",
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Parameter(
        name: "perPage",
        in: "query",
        required: false,
        description: "Number of items per page",
        schema: new OA\Schema(type: "integer", default: 10)
    )]
    #[OA\Parameter(
        name: "currentPage",
        in: "query",
        required: false,
        description: "Current page number",
        schema: new OA\Schema(type: "integer", default: 1)
    )]
    #[OA\Response(response: 200, description: "Success", content: [new OA\JsonContent(
        properties: [
            new OA\Property(property: "products", type: "object"),
            new OA\Property(property: "productsAll", type: "array", items: new OA\Items(ref: "#/components/schemas/ICertifyProduct")),
            new OA\Property(property: "totalPage", type: "integer"),
            new OA\Property(property: "totalProducts", type: "integer"),
        ]
    )])]
    public function getProducts(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', '');
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);

        $productsQuery = CertifyProduct::when($searchValue, function ($queryBuilder) use ($searchValue) {
            $queryBuilder->where('name', 'LIKE', '%' . $searchValue . '%')
                ->orWhere('brand', 'LIKE', '%' . $searchValue . '%')
                ->orWhere('product_code', 'LIKE', '%' . $searchValue . '%');
        });

        $productsAll = (clone $productsQuery)->get();
        $productsPage = $productsQuery->paginate($perPage, ['*'], 'page', $currentPage);

        $totalProducts = $productsPage->total();
        $totalPage = ceil($totalProducts / $perPage);

        return response()->json([
            "products" => $productsPage,
            'productsAll' => $productsAll,
            "totalPage" => $totalPage,
            "totalProducts" => $totalProducts,
        ]);
    }

    /**
     * create a new certify product
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/certify-products/store",
        operationId: "createCertifyProduct",
        description: "Create a new Certify Product",
        tags: ["certify-products"],
    )]
    #[OA\RequestBody(required: true, content: [new OA\JsonContent(
        required: ["name", "price", "tax_rate"],
        properties: [
            new OA\Property(property: "name", type: "string", example: "Sample Product"),
            new OA\Property(property: "brand", type: "string", example: "Sample Brand"),
            new OA\Property(property: "description", type: "string", example: "Product description"),
            new OA\Property(property: "product_code", type: "string", example: "PROD123"),
            new OA\Property(property: "category_id", type: "integer", example: 1),
            new OA\Property(property: "SKU", type: "string", example: "SKU123"),
            new OA\Property(property: "min_stock_level", type: "integer", example: 10),
            new OA\Property(property: "price", type: "number", format: "float", example: 99.99),
            new OA\Property(property: "weight", type: "number", format: "float", example: 0.5),
            new OA\Property(property: "stockable", type: "boolean", example: true),
            new OA\Property(property: "tax_rate", type: "number", format: "float", example: 0.19),
        ]
    )])]
    #[OA\Response(response: 200, description: "Success", content: [new OA\JsonContent(
        ref: "#/components/schemas/ICertifyProduct",
        type: 'object'
    )])]
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'product_code' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'SKU' => 'nullable|string|max:255',
            'min_stock_level' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'stockable' => 'nullable|boolean',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        $product = CertifyProduct::create($validatedData);

        return response()->json(['message' => 'Certify Product created successfully', 'product' => $product]);
    }

    /**
     * update a certify product
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/certify-products/update/{id}",
        operationId: "updateCertifyProduct",
        description: "Update Certify Product",
        tags: ["certify-products"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: [new OA\JsonContent(
        properties: [
            new OA\Property(property: "name", type: "string", example: "Updated Product"),
            new OA\Property(property: "brand", type: "string", example: "Updated Brand"),
            new OA\Property(property: "description", type: "string", example: "Updated description"),
            new OA\Property(property: "product_code", type: "string", example: "PROD456"),
            new OA\Property(property: "category_id", type: "integer", example: 2),
            new OA\Property(property: "SKU", type: "string", example: "SKU456"),
            new OA\Property(property: "min_stock_level", type: "integer", example: 20),
            new OA\Property(property: "price", type: "number", format: "float", example: 149.99),
            new OA\Property(property: "weight", type: "number", format: "float", example: 1.0),
            new OA\Property(property: "stockable", type: "boolean", example: false),
            new OA\Property(property: "tax_rate", type: "number", format: "float", example: 0.20),
        ]
    )])]
    #[OA\Response(response: 200, description: "Success")]
    public function update(int $id, Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'product_code' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'SKU' => 'nullable|string|max:255',
            'min_stock_level' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'stockable' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $product = CertifyProduct::findOrFail($id);
        $product->update($validatedData);

        return response()->json(['message' => 'Certify Product updated successfully', 'product' => $product]);
    }

    /**
     * Delete a certify product
     *
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Delete(
        path: "/api/certify-products/delete/{id}",
        operationId: "deleteCertifyProduct",
        description: "Delete a certify product",
        tags: ["certify-products"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: "Successfully deleted")]
    public function delete(int $id): JsonResponse
    {
        $product = CertifyProduct::findOrFail($id);
        $product->delete();
        return response()->json(["message" => "Certify Product deleted successfully"]);
    }

    /**
     * Import certify products from CSV.
     *
     * Accepted CSV headers:
     * name (required), price (required), tax_rate (optional, defaults to 0),
     * brand, description, product_code, category_id, SKU, min_stock_level, weight, stockable
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

        foreach (['name', 'price'] as $column) {
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
                'name' => $rowData['name'] ?? null,
                'brand' => $rowData['brand'] ?? null,
                'description' => $rowData['description'] ?? null,
                'product_code' => $rowData['product_code'] ?? null,
                'category_id' => ($rowData['category_id'] ?? '') !== '' ? (int) $rowData['category_id'] : null,
                'SKU' => $rowData['sku'] ?? null,
                'min_stock_level' => ($rowData['min_stock_level'] ?? '') !== '' ? (int) $rowData['min_stock_level'] : 0,
                'price' => $rowData['price'] ?? null,
                'weight' => ($rowData['weight'] ?? '') !== '' ? $rowData['weight'] : 0,
                'stockable' => filter_var($rowData['stockable'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                'tax_rate' => ($rowData['tax_rate'] ?? '') !== '' ? $rowData['tax_rate'] : 0,
            ];

            $validator = Validator::make($payload, [
                'name' => 'required|string|max:255',
                'brand' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'product_code' => 'nullable|string|max:255',
                'category_id' => 'nullable|integer|min:0',
                'SKU' => 'nullable|string|max:255',
                'min_stock_level' => 'nullable|integer|min:0',
                'price' => 'required|numeric|min:0',
                'weight' => 'nullable|numeric|min:0',
                'stockable' => 'nullable|boolean',
                'tax_rate' => 'nullable|numeric|min:0|max:100',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            CertifyProduct::create($validator->validated());
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
