<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportAuditEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'actor_id',
        'before_json',
        'after_json',
        'reason',
        'occurred_at',
    ];

    protected $casts = [
        'before_json' => 'array',
        'after_json' => 'array',
        'occurred_at' => 'datetime',
    ];
}
