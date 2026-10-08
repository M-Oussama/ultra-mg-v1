<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportProforma extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'proforma_date',
        'sales_supplier_id',
        'departement_id',
        'created_by',
        'currency',
        'total_quantity',
        'total_amount',
        'notes',
        'status',
    ];

    protected $casts = [
        'proforma_date' => 'date:Y-m-d',
        'total_quantity' => 'integer',
        'total_amount' => 'double',
    ];

    public function supplier()
    {
        return $this->belongsTo(SalesSupplier::class, 'sales_supplier_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'departement_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(ImportProformaItem::class);
    }
}
