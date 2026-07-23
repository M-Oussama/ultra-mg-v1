<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ImportationPayment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'importation_invoice_id',
        'amount',
        'type',
        'percentage_rate',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'percentage_rate' => 'float',
        'payment_date' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(ImportationInvoice::class, 'importation_invoice_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('scans')
             ->acceptsMimeTypes(['application/pdf']);
    }
}
