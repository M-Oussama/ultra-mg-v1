<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleStatus extends Model
{
    use HasFactory, SoftDeletes;

    const PAID = 'PAID';
    const NOT_PAID = 'NOT PAID';
    const PARTIALLY_PAID = 'PARTIALLY PAID';

    protected $fillable = [
        'name'
    ];

    const STATUS = [
        self::PAID,
        self::NOT_PAID,
        self::PARTIALLY_PAID
    ];
}
