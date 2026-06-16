<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Transaction extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'cashbook_id',
        'user_id',
        'organization_id',
        'contact_id',
        'category_id',
        'payment_mode_id',
        'type',
        'amount',
        'note',
        'transaction_date',
        'transaction_time',
        'import_source',
        'source_file_name',
        'source_row_number',
        'source_payload',
    ];

    protected $casts = [
        'transaction_date' => 'date:Y-m-d',
        'amount' => 'decimal:2',
        'source_payload' => 'array',
    ];

    public function cashbook()
    {
        return $this->belongsTo(Cashbook::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function contact()
    {
        return $this->belongsTo(CashbookContact::class)->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo(CashbookCategory::class)->withTrashed();
    }

    public function paymentMode()
    {
        return $this->belongsTo(CashbookPaymentMode::class)->withTrashed();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }
}
