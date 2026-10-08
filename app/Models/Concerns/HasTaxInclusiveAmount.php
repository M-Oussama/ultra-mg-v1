<?php

namespace App\Models\Concerns;

trait HasTaxInclusiveAmount
{
    /** Use tax components rather than amount, which legacy rows store as HT. */
    public function getAmountTtcAttribute(): float
    {
        $ht = (float) ($this->ht_amount ?? $this->amount ?? 0);
        $vat = (float) ($this->tva_amount ?? 0);
        if ($vat <= 0) {
            $vat = round($ht * (float) ($this->tva_rate ?? 19) / 100, 2);
        }

        $stamp = (float) ($this->timbre_amount ?? 0);
        if ($stamp <= 0 && (int) $this->payment_type === 1) {
            $stamp = round(($ht + $vat) * (float) ($this->timbre_rate ?? 0) / 100, 2);
        }

        return round($ht + $vat + $stamp, 2);
    }

    /** Same calculation for efficient database aggregation without loading rows. */
    public static function amountTtcExpression(): string
    {
        // Cast floating legacy columns to decimal before half-cent rounding.
        $ht = 'CAST(COALESCE(ht_amount, amount, 0) AS DECIMAL(24, 6))';
        $vat = '(CASE WHEN COALESCE(tva_amount, 0) > 0 THEN CAST(tva_amount AS DECIMAL(24, 6)) '
            ."ELSE ROUND(CAST(($ht) * COALESCE(tva_rate, 19) / 100.0 AS DECIMAL(24, 6)), 2) END)";
        $stamp = '(CASE WHEN COALESCE(timbre_amount, 0) > 0 THEN CAST(timbre_amount AS DECIMAL(24, 6)) '
            ."WHEN payment_type = 1 THEN ROUND(CAST((($ht) + ($vat)) * COALESCE(timbre_rate, 0) / 100.0 AS DECIMAL(24, 6)), 2) "
            .'ELSE 0 END)';

        return "ROUND(($ht) + ($vat) + ($stamp), 2)";
    }
}
