<?php

namespace SanderVanHooft\Invoicable;

use Illuminate\Support\ServiceProvider;
use SanderVanHooft\Invoicable\Services\BillService;
use SanderVanHooft\Invoicable\Services\Interfaces\BillServiceInterface;
use SanderVanHooft\Invoicable\Services\Interfaces\InvoiceServiceInterface;
use SanderVanHooft\Invoicable\Services\InvoiceService;

class InvoicableServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $sourceViewsPath = __DIR__.'/../resources/views';
        $this->loadViewsFrom($sourceViewsPath, 'invoicable');

        $this->publishes([
            $sourceViewsPath => resource_path('views/vendor/invoicable'),
        ], 'views');

        // Publish a config file
        $this->publishes([
            __DIR__.'/../config/invoicable.php' => config_path('invoicable.php'),
        ], 'config');

        // Publish migrations
         $this->publishes([
             __DIR__.'/../database/migrations/2017_06_17_163005_create_invoices_tables.php'
             => database_path('migrations/2017_06_17_163005_create_invoices_tables.php'),
         ], 'migrations');

        $this->app->bind(InvoiceServiceInterface::class, function ($app) {
            return new InvoiceService(
                $app->make(Invoice::class)
            );
        });
        $this->app->bind(BillServiceInterface::class, function ($app) {
            return new BillService(
                $app->make(Bill::class)
            );
        });
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
