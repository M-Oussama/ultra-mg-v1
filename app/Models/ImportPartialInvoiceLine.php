<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportPartialInvoiceLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_partial_invoice_id',
        'import_parent_invoice_line_id',
        'quantity',
        'commercial_unit_price_snapshot',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'commercial_unit_price_snapshot' => 'decimal:4',
    ];

    public function partialInvoice()
    {
        return $this->belongsTo(ImportPartialInvoice::class, 'import_partial_invoice_id');
    }

    public function parentLine()
    {
        return $this->belongsTo(ImportParentInvoiceLine::class, 'import_parent_invoice_line_id');
    }
}
