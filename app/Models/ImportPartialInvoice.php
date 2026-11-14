<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

class ImportPartialInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::updating(function (self $invoice) {
            if ($invoice->loaded_at !== null && $invoice->isDirty('freight_amount')) {
                throw ValidationException::withMessages([
                    'freight_amount' => 'Freight cannot be changed after loading.',
                ]);
            }
        });
    }

    protected $fillable = [
        'import_parent_invoice_id',
        'number',
        'container_allocation_count',
        'freight_amount',
        'agent_percentage',
        'bank_exchange_rate_dzd',
        'transport_amount',
        'transport_customs_percentage',
        'transport_exchange_rate_dzd',
        'real_invoice_number',
        'real_invoice_date',
        'loaded_at',
        'status',
        'revision',
        'idempotency_key',
    ];

    protected $casts = [
        'container_allocation_count' => 'integer',
        'freight_amount' => 'decimal:4',
        'agent_percentage' => 'decimal:4',
        'bank_exchange_rate_dzd' => 'decimal:4',
        'transport_amount' => 'decimal:4',
        'transport_customs_percentage' => 'decimal:4',
        'transport_exchange_rate_dzd' => 'decimal:4',
        'real_invoice_date' => 'date',
        'loaded_at' => 'datetime',
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

    public function containers()
    {
        return $this->hasMany(ImportContainer::class, 'import_partial_invoice_id');
    }

    public function pdTotals(): array
    {
        $lines = $this->relationLoaded('lines') ? $this->getRelation('lines') : $this->lines()->get();
        $goods = (float) $lines->sum(fn ($line) => (float) $line->quantity * (float) $line->commercial_unit_price_snapshot);
        $quantity = (float) $lines->sum(fn ($line) => (float) $line->quantity);
        $freight = (float) ($this->freight_amount ?? 0);

        return [
            'goods' => $goods,
            'freight' => $freight,
            'total' => $goods + $freight,
            'quantity' => $quantity,
        ];
    }

    public function realTotals(): ?array
    {
        if ($this->loaded_at === null) {
            return null;
        }

        $lines = $this->relationLoaded('lines') ? $this->getRelation('lines') : $this->lines()->get();
        $lines = $lines->filter(fn ($line) => $line->real_quantity !== null);
        $goods = (float) $lines->sum(fn ($line) => (float) $line->real_quantity * (float) $line->real_unit_price);
        $quantity = (float) $lines->sum(fn ($line) => (float) $line->real_quantity);
        $containers = $this->relationLoaded('containers') ? $this->getRelation('containers') : $this->containers()->get();
        $freightValues = $containers->pluck('real_freight_amount')->filter(fn ($amount) => $amount !== null);
        $freight = $freightValues->isEmpty() ? null : (float) $freightValues->sum(fn ($amount) => (float) $amount);

        return [
            'goods' => $goods,
            'freight' => $freight,
            'total' => $goods + ($freight ?? 0),
            'quantity' => $quantity,
        ];
    }

    public function variance(): ?array
    {
        $real = $this->realTotals();
        if ($real === null) {
            return null;
        }

        $pd = $this->pdTotals();
        $totalVariance = $real['total'] - $pd['total'];

        return [
            'quantity' => $real['quantity'] - $pd['quantity'],
            'goods' => $real['goods'] - $pd['goods'],
            'freight' => ($real['freight'] ?? 0) - $pd['freight'],
            'total' => $totalVariance,
            'percent_total' => $pd['total'] == 0.0 ? 0.0 : round($totalVariance / $pd['total'] * 100, 2),
        ];
    }
}
