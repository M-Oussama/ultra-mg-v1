<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_date',
        'client_id',
        'total_amount',
        'sale_statuses_id',
        'notes',
        'balance'
    ];

    protected $with = ['client'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }


    public function saleStatus()
    {
        return $this->belongsTo(SaleStatus::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
