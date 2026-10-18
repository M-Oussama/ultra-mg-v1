<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportParentInvoiceLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_parent_invoice_id',
        'product_id',
        'description_snapshot',
        'unit',
        'quantity',
        'commercial_unit_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'commercial_unit_price' => 'decimal:4',
    ];

    public function parentInvoice()
    {
        return $this->belongsTo(ImportParentInvoice::class, 'import_parent_invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function partialLines()
    {
        return $this->hasMany(ImportPartialInvoiceLine::class);
    }
}
