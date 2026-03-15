<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCertifyInvoiceProducts extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sub_certify_invoice_id',
        'product_id',
        'price',
        'quantity',
        'total',
    ];

    protected $with = ['product'];

    public function subInvoice(): BelongsTo
    {
        return $this->belongsTo(SubCertifyInvoices::class, 'sub_certify_invoice_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(CertifyProduct::class, 'product_id');
    }
}
