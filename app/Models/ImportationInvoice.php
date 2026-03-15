<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportationInvoice extends Model
{
    use HasFactory;

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
        'invoice_pdf',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments()
    {
        return $this->hasMany(ImportationPayment::class, 'importation_invoice_id');
    }
}
