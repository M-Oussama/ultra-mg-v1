<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'month',
        'year',
        'benefit',
        'raw_material_price',
        'raw_material_quantity',
        'raw_material_cost',
        'electricity',
        'employee_salary',
        'other_charges',
        'netBenefit',
        'total_amount',
        'total_articles',
    ];

    protected $casts = [
        'department_id' => 'integer',
        'month' => 'integer',
        'year' => 'integer',
        'benefit' => 'double',
        'raw_material_price' => 'double',
        'raw_material_quantity' => 'double',
        'raw_material_cost' => 'double',
        'electricity' => 'double',
        'employee_salary' => 'double',
        'other_charges' => 'double',
        'netBenefit' => 'double',
        'total_amount' => 'double',
        'total_articles' => 'double',
    ];

    public function batchUsages()
    {
        return $this->hasMany(BenefitBatchUsage::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function extraFees()
    {
        return $this->hasMany(BenefitExtraFee::class)->orderBy('id');
    }
}
