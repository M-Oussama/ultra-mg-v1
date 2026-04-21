<?php

namespace App\Observers;

use App\Models\PartialPayment;

class PartialPaymentObserver
{
    private function updateSale(PartialPayment $partialPayment): void
    {
        if ($partialPayment->sale_id && $partialPayment->sale) {
            $partialPayment->sale->syncPaidAmount();
        }
    }

    public function created(PartialPayment $partialPayment): void
    {
        $this->updateSale($partialPayment);
    }

    public function updated(PartialPayment $partialPayment): void
    {
        $this->updateSale($partialPayment);

        if ($partialPayment->wasChanged('sale_id')) {
            $oldSaleId = $partialPayment->getOriginal('sale_id');
            if ($oldSaleId) {
                $oldSale = \App\Models\Sale::find($oldSaleId);
                if ($oldSale) {
                    $oldSale->syncPaidAmount();
                }
            }
        }
    }

    public function deleted(PartialPayment $partialPayment): void
    {
        $this->updateSale($partialPayment);
    }

    public function restored(PartialPayment $partialPayment): void
    {
        $this->updateSale($partialPayment);
    }
}
