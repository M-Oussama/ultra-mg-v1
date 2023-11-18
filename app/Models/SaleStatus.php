<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleStatus extends Model
{
    use HasFactory, SoftDeletes;

    const PAID = 'PAID';
    const PAID_ID = 1;
    const NOT_PAID = 'NOT PAID';
    const NOT_PAID_ID = 2;
    const PARTIALLY_PAID = 'PARTIALLY PAID';
    const PARTIALLY_PAID_ID = 3;

    protected $fillable = [
        'name'
    ];

    const STATUS = [
        self::PAID,
        self::NOT_PAID,
        self::PARTIALLY_PAID
    ];
}
