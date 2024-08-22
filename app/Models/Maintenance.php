<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'component_id',
        'asset_id',
        'technician_assigned_id',
        'maintenance_date',
        'next_maintenance_date',
        'notes',
        'status',
        'technician_id'
    ];

    protected $with = ['component', 'technician', 'assigned'];

    public function component() {
        return $this->belongsTo(Components::class);
    }
    public function technician() {
        return $this->belongsTo(User::class, 'technician_id');
    }
    public function assigned() {
        return $this->belongsTo(User::class, 'technician_assigned_id');
    }
}
