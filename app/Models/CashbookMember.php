<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashbookMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashbook_id',
        'user_id',
        'role',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function cashbook()
    {
        return $this->belongsTo(Cashbook::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
