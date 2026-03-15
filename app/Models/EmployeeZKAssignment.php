<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeZKAssignment extends Model
{
    use HasFactory;

    protected $table = 'employee_zk_assignments';

    protected $fillable = [
        'employee_id',
        'zk_employee_id',
        'assigned_at',
        'released_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function zkEmployee()
    {
        return $this->belongsTo(ZKEmployee::class, 'zk_employee_id');
    }
}
