<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_date',
        'client_id',
        'total_amount',
        'sale_statuses_id',
        'notes',
        'balance',
        'regulation',
        'driver_id',
        'picked_up',
        'department_id',
        'show_company_info',
        'user_id',
        'paid_amount',
    ];
    protected $casts = [
        'payment' => 'boolean',
        'paid_amount' => 'double',
        'show_company_info' => 'boolean',
    ];
    protected $with = ['client','saleStatus','saleItems', 'driver', 'department', 'user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function saleStatus()
    {
        return $this->belongsTo(SaleStatus::class,'sale_statuses_id');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function driver()
    {
        return $this->belongsTo(TruckDriver::class,'truck_driver_id');
    }

    /**
     * Recalculate and persist the total paid_amount for this sale.
     */
    public function syncPaidAmount(): void
    {
        // Don't touch updated_at
        static::withoutTimestamps(function () {
            $direct_payment = Payment::where('sale_id', $this->id)->sum('amount_paid');
            $partial_payment = PartialPayment::where('sale_id', $this->id)->sum('amount');

            $this->update(['paid_amount' => (float) ($direct_payment + $partial_payment)]);
        });
    }
}
