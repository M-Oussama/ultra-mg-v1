<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ImportationInvoice extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'BL_number',
        'company_name',
        'start_date',
        'arrive_date',
        'amount',
        'container_status',
        'notes',
        'vessel_name',
        'vessel_number',
        'container_number',
        'user_id',
    ];

    protected $appends = ['invoice_pdf'];

    public function getInvoicePdfAttribute()
    {
        $media = $this->getFirstMedia('invoice_pdf');
        return $media ? $media->getUrl() : null;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(ImportationPayment::class , 'importation_invoice_id');
    }
}
