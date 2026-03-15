<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealLogisticsItemsInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'real_logistics_invoice_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
        'total_price',
    ];

    protected $with = ['product'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(RealLogisticsInvoice::class, 'real_logistics_invoice_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
