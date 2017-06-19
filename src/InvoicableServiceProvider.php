<?php

namespace SanderVanHooft\Invoicable;

use Illuminate\Support\ServiceProvider;

class InvoicableServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish a config file
        $this->publishes([
            __DIR__.'/../config/invoicable.php' => config_path('invoicable.php'),
        ], 'config');

        // Publish migrations
         $this->publishes([
             __DIR__.'/../database/migrations/2017_06_17_163005_create_invoices_table.php'
             => database_path('migrations/2017_06_17_163005_create_invoices_table.php'),
         ], 'migrations');

         // Load routes
         // $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/invoicable.php', 'invoicable');
    }
}
