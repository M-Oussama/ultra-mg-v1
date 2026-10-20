<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportParentInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'import_operation_id',
        'sales_supplier_id',
        'number',
        'currency',
        'planned_container_count',
        'freight_per_container',
        'show_container_price',
        'status',
        'revision',
    ];

    protected $casts = [
        'planned_container_count' => 'integer',
        'freight_per_container' => 'decimal:4',
        'show_container_price' => 'boolean',
        'revision' => 'integer',
    ];

    public function supplier()
    {
        return $this->belongsTo(SalesSupplier::class, 'sales_supplier_id');
    }

    public function operation()
    {
        return $this->belongsTo(ImportOperation::class, 'import_operation_id');
    }

    public function lines()
    {
        return $this->hasMany(ImportParentInvoiceLine::class);
    }

    public function partialInvoices()
    {
        return $this->hasMany(ImportPartialInvoice::class);
    }
}
