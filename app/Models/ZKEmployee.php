<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZKEmployee extends Model
{
    use HasFactory;

    protected $table = 'zk_employees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'zk_id',
        'name',
        'surname',
    ];

    /**
     * Since the id is the ZK device PIN/ID, we might want to disable auto-increment 
     * if we are manually assigning it, or keep it if it's just a regular primary key.
     * Given the usual ZK integration, the 'id' is often the 'PIN'.
     */
    public $incrementing = false;
    protected $keyType = 'integer';
}
