<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZKEmployeeAttendance extends Model
{
    use HasFactory;

    protected $table = 'zk_employee_attendances';

    protected $fillable = [
        'employee_id',
        'user_id',
        'punched_at',
        'type',
        'raw_line',
    ];

    protected $casts = [
        'punched_at' => 'datetime',
    ];

    /**
     * Get the user that owns the attendance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
