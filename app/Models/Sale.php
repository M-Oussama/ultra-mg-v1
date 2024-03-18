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
        'balance',
        'regulation'
    ];
    protected $casts = [
        'payment' => 'boolean'
    ];
    protected $with = ['client','saleStatus','saleItems'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function saleStatus()
    {
        return $this->belongsTo(SaleStatus::class,'sale_statuses_id');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
