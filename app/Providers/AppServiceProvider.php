<?php

namespace App\Providers;

use App\Models\Group;
use App\Models\Payment;
use App\Observers\GroupObserver;
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
        Group::observe(GroupObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
