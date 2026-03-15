<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RealLogisticsInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_date',
        'client_id',
        'total_amount',
        'status',
        'notes',
    ];

    protected $with = ['client', 'items'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RealLogisticsItemsInvoice::class);
    }
}
