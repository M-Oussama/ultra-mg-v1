<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'logo_url',
        'profession',
        'department_type',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function getIsProductionAttribute(): bool
    {
        return strtolower((string) ($this->department_type ?? 'resell')) === 'production';
    }
}
