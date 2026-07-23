<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntrepreneurSettlement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'entrepreneur_id',
        'user_id',
        'entrepreneur_name',
        'reference',
        'base_amount',
        'base_direction',
        'currency',
        'settlement_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'base_amount' => 'float',
        'settlement_date' => 'date:Y-m-d',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function entrepreneur()
    {
        return $this->belongsTo(SettlementEntrepreneur::class, 'entrepreneur_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(EntrepreneurSettlementTransaction::class, 'settlement_id');
    }
}
