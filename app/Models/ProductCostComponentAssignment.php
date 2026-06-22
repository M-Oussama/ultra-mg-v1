<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCostComponentAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'component_id',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'component_id' => 'integer',
        'quantity' => 'double',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function component()
    {
        return $this->belongsTo(ProductCostComponent::class, 'component_id');
    }
}
