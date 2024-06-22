<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartialPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_id',
        'sale_id',
        'client_id',
        'amount'
    ];

    public function payment(){
        return $this->belongsTo(Payment::class);
    }
    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function sale(){
        return $this->belongsTo(Sale::class);
    }
}
