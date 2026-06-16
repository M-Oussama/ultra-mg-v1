<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashbookContact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cashbook_id',
        'user_id',
        'organization_id',
        'name',
        'phone',
        'email',
        'notes',
    ];

    public function cashbook()
    {
        return $this->belongsTo(Cashbook::class);
    }

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
        return $this->hasMany(Transaction::class, 'contact_id');
    }
}
