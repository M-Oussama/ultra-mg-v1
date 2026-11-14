<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportContainer extends Model
{
    protected $table = 'import_containers';

    protected $fillable = [
        'real_freight_amount',
        'transitaire_fee_dzd',
    ];

    protected $casts = [
        'real_freight_amount' => 'decimal:4',
        'transitaire_fee_dzd' => 'decimal:4',
    ];
}
