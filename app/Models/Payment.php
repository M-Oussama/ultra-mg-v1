<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'amount_paid',
        'payment_date'
    ];

    protected $with = [
        'client'
    ];
    public function sale() {
        return $this->belongsTo(Sale::class);
    }
    public function client() {
        return $this->belongsTo(Client::class);
    }
}
