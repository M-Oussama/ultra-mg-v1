<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class ImportParentInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::updating(function (self $invoice) {
            if ($invoice->domiciled_at !== null && ($invoice->isDirty('planned_container_count') || $invoice->isDirty('freight_per_container'))) {
                throw ValidationException::withMessages([
                    'parent_invoice' => 'Planned containers and freight cannot be changed after domiciliation.',
                ]);
            }
        });
    }

    protected $fillable = [
        'import_operation_id',
        'sales_supplier_id',
        'number',
        'currency',
        'planned_container_count',
        'freight_per_container',
        'domiciled_at',
        'closed_at',
        'show_container_price',
        'show_ports',
        'status',
        'revision',
    ];

    protected $casts = [
        'planned_container_count' => 'integer',
        'freight_per_container' => 'decimal:4',
        'domiciled_at' => 'datetime',
        'closed_at' => 'datetime',
        'show_container_price' => 'boolean',
        'show_ports' => 'boolean',
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

    public function pdTotals(): array
    {
        $lines = $this->relationLoaded('lines') ? $this->getRelation('lines') : $this->lines()->get();
        $goods = (float) $lines->sum(fn ($line) => (float) $line->quantity * (float) $line->commercial_unit_price);
        $quantity = (float) $lines->sum(fn ($line) => (float) $line->quantity);
        $freight = (float) ($this->planned_container_count ?? 0) * (float) ($this->freight_per_container ?? 0);

        return [
            'goods' => $goods,
            'freight' => $freight,
            'total' => $goods + $freight,
            'quantity' => $quantity,
        ];
    }

    public function realTotals(): array
    {
        $partials = $this->relationLoaded('partialInvoices')
            ? $this->getRelation('partialInvoices')
            : $this->partialInvoices()->get();
        $loaded = $partials->filter(fn ($partial) => $partial->loaded_at !== null);
        $totals = $loaded->map(fn ($partial) => $partial->realTotals());

        return [
            'goods' => (float) $totals->sum('goods'),
            'freight' => (float) $totals->sum('freight'),
            'total' => (float) $totals->sum('total'),
            'quantity' => (float) $totals->sum('quantity'),
            'loaded_count' => $loaded->count(),
            'partial_count' => $partials->count(),
        ];
    }

    public function variance(): array
    {
        $pd = $this->pdTotals();
        $real = $this->realTotals();

        return [
            'quantity' => $real['quantity'] - $pd['quantity'],
            'goods' => $real['goods'] - $pd['goods'],
            'freight' => $real['freight'] - $pd['freight'],
            'total' => $real['total'] - $pd['total'],
            'unused' => [
                'quantity' => $pd['quantity'] - $real['quantity'],
                'goods' => $pd['goods'] - $real['goods'],
                'freight' => $pd['freight'] - $real['freight'],
                'total' => $pd['total'] - $real['total'],
            ],
        ];
    }
}
