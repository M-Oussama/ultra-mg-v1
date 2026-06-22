<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExtraCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'amount',
        'effective_from',
        'effective_to',
        'notes',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'amount' => 'double',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
