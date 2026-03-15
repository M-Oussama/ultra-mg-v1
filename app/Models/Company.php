<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'address',
        'address2',
        'phone',
        'email',
        'NRC',
        'NIF',
        'NART',
        'NIS',
        'capitale'
    ];
}

