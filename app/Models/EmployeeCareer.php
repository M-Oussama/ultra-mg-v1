<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EmployeeCareer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'position',
        'position_ar',
        'real_start_date',
        'real_end_date'
    ];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }
}
