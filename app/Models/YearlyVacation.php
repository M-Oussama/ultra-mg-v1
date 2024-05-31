<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlyVacation extends Model
{
    use HasFactory;

    protected $fillable = [
      'start_date',
      'end_date',
      'count',
      'employee_id',
      'employee_career_id'
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function employee_career(){
        return $this->belongsTo(EmployeeCareer::class);
    }
}
