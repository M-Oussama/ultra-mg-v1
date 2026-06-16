<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cashbook extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'user_id', 'organization_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function contacts()
    {
        return $this->hasMany(CashbookContact::class);
    }

    public function categories()
    {
        return $this->hasMany(CashbookCategory::class);
    }

    public function paymentModes()
    {
        return $this->hasMany(CashbookPaymentMode::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'cashbook_members')
            ->withPivot(['id', 'role', 'permissions'])
            ->withTimestamps();
    }

    public function memberAssignments()
    {
        return $this->hasMany(CashbookMember::class);
    }

    public function getBalanceAttribute()
    {
        if (array_key_exists('balance', $this->attributes) && $this->attributes['balance'] !== null) {
            return (float) $this->attributes['balance'];
        }

        $income = (float) ($this->attributes['total_income'] ?? 0);
        $expense = (float) ($this->attributes['total_expense'] ?? 0);

        if ($income !== 0.0 || $expense !== 0.0) {
            return $income - $expense;
        }

        $income = (float) $this->transactions()->where('type', 'income')->sum('amount');
        $expense = (float) $this->transactions()->where('type', 'expense')->sum('amount');

        return $income - $expense;
    }
}
