<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenefitBatchUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'benefit_id',
        'supply_item_id',
        'quantity_used',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity_used' => 'double',
        'unit_price' => 'double',
        'total_price' => 'double',
    ];

    protected $with = ['supplyItem.product', 'supplyItem.supply.supplier'];

    public function benefit()
    {
        return $this->belongsTo(Benefit::class);
    }

    public function supplyItem()
    {
        return $this->belongsTo(SupplyItem::class);
    }
}
