<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeMonthlyPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'monthly_salary',
        'objectives_amount',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'monthly_salary' => 'decimal:2',
        'objectives_amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
