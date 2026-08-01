<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeMonthlyWorkDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'work_days',
        'cnas_days',
        'out_date',
        'in_date',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'work_days' => 'integer',
        'cnas_days' => 'integer',
    ];

    public function setOutDateAttribute($value): void
    {
        $this->attributes['out_date'] = $this->normalizeDateForStorage($value);
    }

    public function setInDateAttribute($value): void
    {
        $this->attributes['in_date'] = $this->normalizeDateForStorage($value);
    }

    public function getOutDateAttribute($value): ?string
    {
        return $this->formatDateForDisplay($value);
    }

    public function getInDateAttribute($value): ?string
    {
        return $this->formatDateForDisplay($value);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    private function normalizeDateForStorage($value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        foreach (['d/m/Y', 'Y-m-d', 'Y/m/d', 'd-m-Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $text)->format('Y-m-d');
            } catch (\Throwable $e) {
                // Try the next format.
            }
        }

        try {
            return Carbon::parse($text)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function formatDateForDisplay($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('d/m/Y');
        }

        try {
            return Carbon::parse($value)->format('d/m/Y');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
