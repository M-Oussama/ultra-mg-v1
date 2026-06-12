<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'department_id',
        'user_id',
        'before_quantity',
        'delta_quantity',
        'after_quantity',
        'reason',
        'source_type',
        'source_id',
        'note',
        'meta',
    ];

    protected $casts = [
        'before_quantity' => 'decimal:3',
        'delta_quantity' => 'decimal:3',
        'after_quantity' => 'decimal:3',
        'meta' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
