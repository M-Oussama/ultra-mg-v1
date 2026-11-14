<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ImportPartialInvoiceLine extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::updating(function (self $line) {
            if (!$line->isDirty('quantity') && !$line->isDirty('commercial_unit_price_snapshot')) {
                return;
            }

            $partial = $line->relationLoaded('partialInvoice')
                ? $line->getRelation('partialInvoice')
                : $line->partialInvoice()->first();
            if ($partial?->loaded_at !== null) {
                throw ValidationException::withMessages([
                    'partial_invoice_line' => 'Partial invoice lines cannot be changed after loading.',
                ]);
            }
        });
    }

    protected $fillable = [
        'import_partial_invoice_id',
        'import_parent_invoice_line_id',
        'quantity',
        'commercial_unit_price_snapshot',
        'real_quantity',
        'real_unit_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'commercial_unit_price_snapshot' => 'decimal:4',
        'real_quantity' => 'decimal:3',
        'real_unit_price' => 'decimal:4',
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
