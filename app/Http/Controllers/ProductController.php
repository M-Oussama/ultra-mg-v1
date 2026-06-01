<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    /**
     * Get List Of All Products
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(
        path: "/api/products/list",
        operationId: "getProducts",
        description: "Returns the list of products",
        tags: ["products"],
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: [
            new OA\JsonContent(
                ref: "#/components/schemas/IProduct",
                type: 'object'
            )
        ]
    )]
    public function getProducts(Request $request): JsonResponse
    {
        $searchValue = $request->input('searchValue', '');
        $perPage = $request->input('perPage', 10);
        $currentPage = $request->input('currentPage', 1);
        $department_id = $request->input('department_id', '');

        $products = Product::when($searchValue, function ($queryBuilder) use ($searchValue) {
            $queryBuilder->where('name', 'LIKE', '%' . $searchValue . '%')
                ->orWhere('brand', 'LIKE', '%' . $searchValue . '%');
        })->when($department_id, function ($queryBuilder) use ($department_id) {
            $queryBuilder->where('department_id', $department_id);
        })->paginate($perPage, ['*'], 'page', $currentPage);

        $totalProducts = $products->total();
        $totalPage = ceil($totalProducts / $perPage);

        return response()->json(["products" => $products, "totalPage" => $totalPage, "totalProducts" => $totalProducts]);
    }

    /**
     * Create a new product
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/products/store",
        operationId: "createProduct",
        description: "Create a new Product",
        tags: ["products"],
    )]
    #[OA\RequestBody(
        required: true,
        content: [
            new OA\JsonContent(
                required: ["name", "brand", "description", "product_code", "category_id", "SKU", "min_stock_level", "price", "is_available", "tax_rate", "type_id"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Sample Product"),
                    new OA\Property(property: "brand", type: "string", example: "Sample Brand"),
                    new OA\Property(property: "description", type: "string", example: "Product Description"),
                    new OA\Property(property: "product_code", type: "string", example: "PROD123"),
                    new OA\Property(property: "category_id", type: "integer", example: "1"),
                    new OA\Property(property: "SKU", type: "string", example: "SKU123"),
                    new OA\Property(property: "min_stock_level", type: "integer", example: "10"),
                    new OA\Property(property: "price", type: "number", format: "float", example: "99.99"),
                    new OA\Property(property: "is_available", type: "boolean", example: true),
                    new OA\Property(property: "tax_rate", type: "number", format: "float", example: "0.08"),
                ]
            )
        ]
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: [
            new OA\JsonContent(
                ref: "#/components/schemas/IProduct",
                type: 'object'
            )
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'product_code' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'SKU' => 'nullable|string|max:255',
            'min_stock_level' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'stockable' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric',
            'weight' => 'nullable',
            'department_id' => 'nullable|integer',
            'package_type' => 'nullable|string|max:255',
            'units_per_package' => 'nullable|integer|min:0',
        ]);
 
        $validatedData['department_id'] = $request->input('department_id', 1);



        $product = Product::create($validatedData);
        ProductStock::create(
            [
                'product_id' => $product->id,
            ]
        );

        return response()->json(['message' => 'Product created successfully', 'product' => $product]);
    }

    /**
     * Update a product
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: "/api/products/update/{id}",
        operationId: "updateProduct",
        description: "Update a Product",
        tags: ["products"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(
        required: true,
        content: [
            new OA\JsonContent(
                required: ["name", "brand", "description", "product_code", "category_id", "SKU", "min_stock_level", "price", "is_available", "tax_rate", "type_id"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Sample Product"),
                    new OA\Property(property: "brand", type: "string", example: "Sample Brand"),
                    new OA\Property(property: "description", type: "string", example: "Product Description"),
                    new OA\Property(property: "product_code", type: "string", example: "PROD123"),
                    new OA\Property(property: "category_id", type: "integer", example: "1"),
                    new OA\Property(property: "SKU", type: "string", example: "SKU123"),
                    new OA\Property(property: "min_stock_level", type: "integer", example: "10"),
                    new OA\Property(property: "price", type: "number", format: "float", example: "99.99"),
                    new OA\Property(property: "is_available", type: "boolean", example: true),
                    new OA\Property(property: "tax_rate", type: "number", format: "float", example: "0.08"),
                    new OA\Property(property: "type_id", type: "integer", example: "1"),
                ]
            )
        ]
    )]
    #[OA\Response(
        response: 200,
        description: "Success",
        content: [
            new OA\JsonContent(
                ref: "#/components/schemas/IProduct",
                type: 'object'
            )
        ]
    )]
    public function update(int $id, Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'product_code' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'SKU' => 'nullable|string|max:255',
            'min_stock_level' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'stockable' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric',
            'type_id' => 'nullable|integer',
            'weight' => 'nullable',
            'department_id' => 'nullable|integer',
            'package_type' => 'nullable|string|max:255',
            'units_per_package' => 'nullable|integer|min:0',
        ]);

        $validatedData['department_id'] = $request->input('department_id', 1);

        $product = Product::find($id);

        $product->update($validatedData);

        $product->stockable = $request->input('stockable');
        $product->save();

        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }

    /**
     * Delete a product
     *
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Delete(
        path: "/api/products/delete/{id}",
        operationId: "deleteProduct",
        description: "Delete a Product",
        tags: ["products"],
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: 200,
        description: "Successfully deleted",
        content: new OA\JsonContent(type: "boolean", example: true)
    )]
    public function delete(int $id): JsonResponse
    {
        $product = Product::find($id);
        $product->delete();
        return response()->json(["message" => "Product deleted successfully"]);
    }

    /**
     * Import products from CSV with one department_id for all rows.
     *
     * Required CSV headers:
     * id, name, brand, description, product_code, category_id, SKU, min_stock_level, price, weight, stockable, tax_rate
     */
    public function importCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        $departmentId = (int) $request->input('department_id');
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

        foreach (['id', 'name', 'brand', 'description', 'product_code', 'category_id', 'sku', 'min_stock_level', 'price', 'weight', 'stockable', 'tax_rate', 'package_type', 'units_per_package'] as $column) {
            if (!in_array($column, $normalizedHeader, true)) {
                fclose($handle);
                return response()->json(['message' => "Missing required CSV column: {$column}"], 422);
            }
        }

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

            $stockableRaw = strtolower((string) ($rowData['stockable'] ?? '0'));
            $stockable = in_array($stockableRaw, ['1', 'true', 'yes'], true) ? 1 : 0;

            $payload = [
                'id' => isset($rowData['id']) ? (int) $rowData['id'] : null,
                'department_id' => $departmentId,
                'name' => $rowData['name'] ?? null,
                'brand' => $rowData['brand'] ?? null,
                'description' => $rowData['description'] ?? null,
                'product_code' => $rowData['product_code'] ?? null,
                'category_id' => isset($rowData['category_id']) ? (int) $rowData['category_id'] : null,
                'SKU' => $rowData['sku'] ?? null,
                'min_stock_level' => isset($rowData['min_stock_level']) ? (int) $rowData['min_stock_level'] : 0,
                'price' => isset($rowData['price']) ? (float) $rowData['price'] : 0,
                'weight' => isset($rowData['weight']) ? (float) $rowData['weight'] : 0,
                'stockable' => $stockable,
                'tax_rate' => isset($rowData['tax_rate']) ? (float) $rowData['tax_rate'] : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $validator = Validator::make($payload, [
                'id' => 'required|integer|min:1',
                'department_id' => 'required|integer|exists:departments,id',
                'name' => 'required|string|max:255',
                'brand' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'product_code' => 'nullable|string|max:255',
                'category_id' => 'nullable|integer',
                'SKU' => 'nullable|string|max:255',
                'min_stock_level' => 'nullable|integer|min:0',
                'price' => 'nullable|numeric|min:0',
                'weight' => 'nullable|numeric|min:0',
                'stockable' => 'nullable|boolean',
                'tax_rate' => 'nullable|numeric|min:0',
                'package_type' => 'nullable|string|max:255',
                'units_per_package' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            if (!empty($payload['id']) && DB::table('products')->where('id', $payload['id'])->exists()) {
                $warnings[] = [
                    'row' => $rowNumber,
                    'warning' => 'Product id exists, inserted with auto-generated id.',
                ];
                unset($payload['id']);
            }

            DB::table('products')->insert($payload);
            $newProductId = (int) DB::getPdo()->lastInsertId();

            if ($newProductId > 0 && !DB::table('product_stocks')->where('product_id', $newProductId)->exists()) {
                DB::table('product_stocks')->insert([
                    'product_id' => $newProductId,
                    'quantity' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

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


