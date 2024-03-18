<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $with = ['city'];
    protected $fillable = [
        'name',
        'surname',
        'address',
        'phone',
        'NRC',
        'NIF',
        'NIS',
        'NART',
        'email',
        'city_id',
    ];

    public function city(){
        return $this->belongsTo(City::class);
    }
}
