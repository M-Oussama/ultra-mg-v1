<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supply_date',
        'sales_supplier_id',
        'departement_id',
        'total_amount',
        'notes'
    ];

    protected $with = ['supplier', 'department', 'items'];

    public function supplier()
    {
        return $this->belongsTo(SalesSupplier::class, 'sales_supplier_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'departement_id');
    }

    public function items()
    {
        return $this->hasMany(SupplyItem::class);
    }
}
