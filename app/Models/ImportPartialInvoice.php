<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportPartialInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'import_parent_invoice_id',
        'number',
        'container_allocation_count',
        'freight_amount',
        'status',
        'revision',
        'idempotency_key',
    ];

    protected $casts = [
        'container_allocation_count' => 'integer',
        'freight_amount' => 'decimal:4',
        'revision' => 'integer',
    ];

    public function parentInvoice()
    {
        return $this->belongsTo(ImportParentInvoice::class, 'import_parent_invoice_id');
    }

    public function lines()
    {
        return $this->hasMany(ImportPartialInvoiceLine::class);
    }
}
