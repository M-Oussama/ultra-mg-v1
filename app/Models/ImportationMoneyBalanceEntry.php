<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company;
use App\Models\User;

class ImportationMoneyBalanceEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'year',
        'month',
        'group_title',
        'direction',
        'affects_balance',
        'amount',
        'note',
        'in_usd',
        'out_usd',
        'rest_usd',
        'total_5_percent',
        'half_out_usd',
        'rest_percent',
        'total_in',
        'containers_fees',
        'total_out',
        'entry_date',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'float',
        'in_usd' => 'float',
        'out_usd' => 'float',
        'rest_usd' => 'float',
        'total_5_percent' => 'float',
        'half_out_usd' => 'float',
        'rest_percent' => 'float',
        'total_in' => 'float',
        'containers_fees' => 'float',
        'total_out' => 'float',
        'entry_date' => 'date:Y-m-d',
        'year' => 'integer',
        'month' => 'integer',
        'affects_balance' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
