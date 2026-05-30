<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'total_amount',
        'client_id',
        'department_id',
        'date',
        'paid'
    ];

    protected $with = ['client', 'department'];

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function ProductReturnList()
    {
        return $this->hasMany(ProductReturnList::class, 'return_id','id');
    }
}
