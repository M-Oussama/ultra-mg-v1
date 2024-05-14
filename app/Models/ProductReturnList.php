<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReturnList extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'return_id',
        'product_id',
        'quantity',
        'price',
        'total_price',
        'client_id',
        'date'
    ];
    protected $with = ['product'];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
