<?php

namespace App\Providers;

use App\Models\PartialPayment;
use App\Models\Payment;
use App\Observers\PartialPaymentObserver;
use App\Observers\PaymentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Payment::observe(PaymentObserver::class);
        PartialPayment::observe(PartialPaymentObserver::class);
    }
}
