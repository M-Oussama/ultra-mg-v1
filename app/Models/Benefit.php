<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'benefit',
        'raw_material_price',
        'electricity',
        'employee_salary',
        'other_charges',
        'netBenefit',
        'total_amount',
        'total_articles',
    ];
}
