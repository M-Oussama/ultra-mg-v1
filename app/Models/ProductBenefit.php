<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBenefit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'benefit_id',
        'raw_material_price',
        'weight',
        'benefit',
        'product_price',
        'quantity',
        'total_amount',
        'total_profit',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
