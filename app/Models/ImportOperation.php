<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportOperation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'legacy_importation_invoice_id',
        'exporter_supplier_id',
        'created_by',
        'reference',
        'currency',
        'origin',
        'destination',
        'status',
        'notes',
    ];

    public function exporter()
    {
        return $this->belongsTo(Supplier::class, 'exporter_supplier_id');
    }

    public function legacyInvoice()
    {
        return $this->belongsTo(ImportationInvoice::class, 'legacy_importation_invoice_id');
    }

    public function parentInvoices()
    {
        return $this->hasMany(ImportParentInvoice::class);
    }
}
