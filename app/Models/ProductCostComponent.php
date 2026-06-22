<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCostComponent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'cost_type',
        'notes',
    ];

    protected $casts = [
        'cost_type' => 'string',
    ];

    public function prices()
    {
        return $this->hasMany(ProductCostComponentPrice::class, 'component_id')
            ->orderByDesc('effective_from')
            ->orderByDesc('id');
    }

    public function assignments()
    {
        return $this->hasMany(ProductCostComponentAssignment::class, 'component_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_cost_component_assignments', 'component_id', 'product_id')
            ->withPivot(['quantity', 'notes'])
            ->withTimestamps();
    }
}
