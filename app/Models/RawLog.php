<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawLog extends Model
{
    use HasFactory;

    protected $table = 'raw_logs';

    protected $fillable = [
      'user_id',
      'timestamp'
    ];
}
