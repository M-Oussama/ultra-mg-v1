<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Sale;

class PaymentObserver
{
    private function updateSale(Payment $payment): void
    {
        if ($payment->sale_id && $payment->sale) {
            $payment->sale->syncPaidAmount();
        }
    }

    public function created(Payment $payment): void
    {
        $this->updateSale($payment);
    }

    public function updated(Payment $payment): void
    {
        $this->updateSale($payment);

        if ($payment->wasChanged('sale_id')) {
            $oldSaleId = $payment->getOriginal('sale_id');
            if ($oldSaleId) {
                $oldSale = \App\Models\Sale::find($oldSaleId);
                if ($oldSale) {
                    $oldSale->syncPaidAmount();
                }
            }
        }
    }

    public function deleted(Payment $payment): void
    {
        $this->updateSale($payment);
    }

    public function restored(Payment $payment): void
    {
        $this->updateSale($payment);
    }
}
