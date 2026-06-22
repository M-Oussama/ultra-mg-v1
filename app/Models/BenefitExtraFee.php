<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenefitExtraFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'benefit_id',
        'name',
        'amount',
        'notes',
    ];

    protected $casts = [
        'benefit_id' => 'integer',
        'amount' => 'double',
    ];

    public function benefit()
    {
        return $this->belongsTo(Benefit::class);
    }
}
