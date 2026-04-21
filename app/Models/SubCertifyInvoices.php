<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCertifyInvoices extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'certify_invoice_id',
        'fac_id',
        'date',
        'client_id',
        'amount',
        'payment_type',
        'tva_rate',
        'tva_amount',
        'ht_amount',
        'timbre_rate',
        'timbre_amount',
        'cheque_number',
        'cheque_id',
    ];

    protected $with = [
        'client',
        'subCertifyInvoiceProducts',
        'cheque'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(CertifyClient::class, 'client_id');
    }

    public function cheque(): BelongsTo
    {
        return $this->belongsTo(Cheque::class, 'cheque_id');
    }

    public function mainInvoice(): BelongsTo
    {
        return $this->belongsTo(CertifyInvoices::class, 'certify_invoice_id');
    }

    public function subCertifyInvoiceProducts(): HasMany
    {
        return $this->hasMany(SubCertifyInvoiceProducts::class, 'sub_certify_invoice_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(CertifyProduct::class, 'sub_certify_invoice_products', 'sub_certify_invoice_id', 'product_id');
    }
}
