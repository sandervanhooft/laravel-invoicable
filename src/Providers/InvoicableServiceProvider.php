<?php

namespace NeptuneSoftware\Invoicable\Providers;

use Illuminate\Support\ServiceProvider;
use NeptuneSoftware\Invoicable\Models\Bill;
use NeptuneSoftware\Invoicable\Models\Invoice;
use NeptuneSoftware\Invoicable\Services\BillService;
use NeptuneSoftware\Invoicable\Interfaces\BillServiceInterface;
use NeptuneSoftware\Invoicable\Interfaces\InvoiceServiceInterface;
use NeptuneSoftware\Invoicable\Services\InvoiceService;

class InvoicableServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $sourceViewsPath = __DIR__ . '/../../resources/views';
        $this->loadViewsFrom($sourceViewsPath, 'invoicable');

        $this->publishes([
            $sourceViewsPath => resource_path('views/vendor/invoicable'),
        ], 'views');

        // Publish a config file
        $this->publishes([
            __DIR__ . '/../../config/invoicable.php' => config_path('invoicable.php'),
        ], 'config');

        // Publish migrations
         $this->publishes([
             __DIR__ . '/../../database/migrations/2017_06_17_163005_create_invoices_tables.php'
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
        $this->mergeConfigFrom(__DIR__ . '/../../config/invoicable.php', 'invoicable');
    }
}
