<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceActiveEmployeeMonth extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'copied_from_month',
        'copied_from_year',
        'confirmed_at',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'copied_from_month' => 'integer',
        'copied_from_year' => 'integer',
        'confirmed_at' => 'datetime',
    ];
}
