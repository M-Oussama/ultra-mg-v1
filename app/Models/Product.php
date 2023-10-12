<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
        schema: "Product",
        required: ["id", "name", "brand", "description", "product_code", "category_id", "SKU", "min_stock_level", "price", "is_available", "tax_rate", "type_id", "created_at", "updated_at"],
        properties: [
            new OA\Property(property: "id", type: "integer", example: "1"),
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
            new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2023-08-13T12:00:00Z"),
            new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2023-08-13T14:30:00Z"),
        ]
    )
]

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'description',
        'product_code',
        'category_id',
        'SKU',
        'min_stock_level',
        'price',
        'is_available',
        'tax_rate',
        'type_id'
    ];
}
