<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntrepreneurSettlementTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'settlement_id',
        'user_id',
        'direction',
        'amount',
        'description',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'float',
        'transaction_date' => 'date:Y-m-d',
    ];

    public function settlement()
    {
        return $this->belongsTo(EntrepreneurSettlement::class, 'settlement_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
