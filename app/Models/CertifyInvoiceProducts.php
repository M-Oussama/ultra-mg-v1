<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertifyInvoiceProducts extends Model
{
    use HasFactory;

    protected $fillable = [
        'certify_invoice_id',
        'product_id',
        'price',
        'quantity',
        'total',
    ];
}
