<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Components extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'asset_id'
    ];

    protected $with = ['asset'];

    public function asset(){
        return $this->belongsTo(Asset::class);
    }
}
