<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ImportParentInvoiceLine extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::updating(function (self $line) {
            if (!$line->isDirty('quantity') && !$line->isDirty('commercial_unit_price')) {
                return;
            }

            $parent = $line->relationLoaded('parentInvoice')
                ? $line->getRelation('parentInvoice')
                : $line->parentInvoice()->first();
            if ($parent?->domiciled_at !== null) {
                throw ValidationException::withMessages([
                    'parent_invoice_line' => 'Parent invoice lines cannot be changed after domiciliation.',
                ]);
            }
        });
    }

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
