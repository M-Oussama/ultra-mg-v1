<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProductCostComponentPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'component_id',
        'amount',
        'quantity',
        'effective_from',
        'effective_to',
        'notes',
    ];

    protected $casts = [
        'component_id' => 'integer',
        'amount' => 'double',
        'quantity' => 'double',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    protected $appends = [
        'remaining_quantity',
    ];

    public function component()
    {
        return $this->belongsTo(ProductCostComponent::class, 'component_id');
    }

    public function getRemainingQuantityAttribute(): ?float
    {
        if ($this->quantity === null) {
            return null;
        }

        $query = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->join('product_cost_component_assignments', function ($join) {
                $join->on('product_cost_component_assignments.product_id', '=', 'sale_items.product_id')
                    ->where('product_cost_component_assignments.component_id', '=', $this->component_id);
            });

        if ($this->effective_from) {
            $query->whereDate('sales.sale_date', '>=', $this->effective_from->toDateString());
        }

        if ($this->effective_to) {
            $query->whereDate('sales.sale_date', '<=', $this->effective_to->toDateString());
        }

        $componentType = strtolower(str_replace('-', '_', (string) ($this->component?->cost_type ?? 'extra')));
        $consumptionExpression = $componentType === 'raw_material'
            ? 'COALESCE(sale_items.quantity, 0) * (COALESCE(products.weight, 0) / 1000) * COALESCE(product_cost_component_assignments.quantity, 1)'
            : 'COALESCE(sale_items.quantity, 0) * COALESCE(product_cost_component_assignments.quantity, 1)';

        $consumed = (float) $query->sum(DB::raw($consumptionExpression));

        return max(0.0, round((float) $this->quantity - $consumed, 3));
    }
}
