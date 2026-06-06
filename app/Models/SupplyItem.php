<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supply_id',
        'product_id',
        'sales_supplier_id',
        'reference',
        'quantity',
        'unit_price',
        'total_price',
        'supply_date'
    ];

    protected $with = ['product'];

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(SalesSupplier::class, 'sales_supplier_id');
    }
}
