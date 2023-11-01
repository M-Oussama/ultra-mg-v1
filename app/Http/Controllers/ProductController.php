<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $products = Product::when($searchValue, function ($queryBuilder) use ($searchValue) {
            $queryBuilder->where('name', 'LIKE', '%' . $searchValue . '%')
                ->orWhere('brand', 'LIKE', '%' . $searchValue . '%');
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
            'brand' => 'string|max:255',
            'description' => 'string|max:255',
            'product_code' => 'string|max:255',
            'category_id' => 'integer',
            'SKU' => 'string|max:255',
            'min_stock_level' => 'integer',
            'price' => 'numeric',
            'stockable' => 'boolean',
            'tax_rate' => 'numeric',
        ]);

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
            'name' => 'string|max:255',
            'brand' => 'string|max:255',
            'description' => 'string|max:255',
            'product_code' => 'string|max:255',
            'category_id' => 'integer',
            'SKU' => 'string|max:255',
            'min_stock_level' => 'integer',
            'price' => 'numeric',
            'stockable' => 'boolean',
            'tax_rate' => 'numeric',
            'type_id' => 'integer',
        ]);

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
}
