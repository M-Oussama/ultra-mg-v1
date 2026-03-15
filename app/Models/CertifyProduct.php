<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ICertifyProduct",
    required: ["id", "name", "price", "tax_rate", "created_at", "updated_at"],
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
        new OA\Property(property: "weight", type: "number", format: "float", example: "0.5"),
        new OA\Property(property: "stockable", type: "boolean", example: true),
        new OA\Property(property: "tax_rate", type: "number", format: "float", example: "0.19"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-02-11T12:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-02-11T14:30:00Z"),
    ]
)]

class CertifyProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'certify_products';

    protected $fillable = [
        'name',
        'brand',
        'description',
        'product_code',
        'category_id',
        'SKU',
        'min_stock_level',
        'price',
        'weight',
        'stockable',
        'tax_rate',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'stockable' => 'boolean',
        'min_stock_level' => 'integer',
    ];

    public static function getAllProductsFormatted()
    {
        return static::all()->map(function ($product) {
            return [
                'quantity' => 0, // Certify products don't seem to have stock management yet
                'price' => $product->price,
                'product' => $product,
                'id' => $product->id,
            ];
        });
    }
}
