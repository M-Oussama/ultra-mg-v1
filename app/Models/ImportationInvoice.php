<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ImportationInvoice extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'BL_number',
        'company_name',
        'start_date',
        'arrive_date',
        'amount',
        'container_status',
        'is_paid',
        'payment_status',
        'freight_cost',
        'customs_cost',
        'transport_freight_cost',
        'supplier_percentage_rate',
        'notes',
        'vessel_name',
        'vessel_number',
        'container_number',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'is_paid' => 'boolean',
        'freight_cost' => 'float',
        'customs_cost' => 'float',
        'transport_freight_cost' => 'float',
        'supplier_percentage_rate' => 'float',
    ];

    protected $appends = [
        'invoice_pdf',
        'supplier_percentage_amount',
        'total_due_amount',
        'total_paid_amount',
        'remaining_balance',
    ];

    public function getInvoicePdfAttribute()
    {
        $media = $this->getFirstMedia('invoice_pdf');
        return $media ? $media->getUrl() : null;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(ImportationPayment::class , 'importation_invoice_id');
    }

    public function getSupplierPercentageAmountAttribute(): float
    {
        $rate = (float) ($this->supplier_percentage_rate ?? 0);
        if ($rate <= 0) {
            return 0.0;
        }

        return round(((float) $this->amount) * ($rate / 100), 2);
    }

    public function getTotalDueAmountAttribute(): float
    {
        return round(
            (float) $this->amount
            + (float) ($this->freight_cost ?? 0)
            + (float) ($this->customs_cost ?? 0)
            + (float) ($this->transport_freight_cost ?? 0)
            + (float) $this->supplier_percentage_amount,
            2
        );
    }

    public function getTotalPaidAmountAttribute(): float
    {
        if ($this->relationLoaded('payments')) {
            return round((float) $this->payments->sum('amount'), 2);
        }

        return round((float) $this->payments()->sum('amount'), 2);
    }

    public function getRemainingBalanceAttribute(): float
    {
        return round(max(0, $this->total_due_amount - $this->total_paid_amount), 2);
    }
}
