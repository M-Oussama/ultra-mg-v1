<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'supplier_id',
        'amount',
        'status_id',
        'notes',
        'balance',
        'regulation'
    ];
    protected $casts = [
        'payment' => 'boolean'
    ];
    protected $with = ['supplier'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

}
