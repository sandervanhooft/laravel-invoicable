<?php

namespace NeptuneSoftware\Invoicable;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use NeptuneSoftware\Invoicable\Providers\InvoicableServiceProvider;

class AbstractTestCase extends AbstractPackageTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Get the service provider class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return string
     */
    protected function getServiceProviderClass($app)
    {
        return InvoicableServiceProvider::class;
    }

    protected function setUp() : void
    {
        parent::setUp();
        $this->withPackageMigrations();
    }

    protected function withPackageMigrations()
    {
        include_once __DIR__.'/CreateTestModelsTable.php';
        (new \NeptuneSoftware\Invoicable\CreateTestModelsTable())->up();
        include_once __DIR__.'/../database/migrations/2017_06_17_163005_create_invoices_tables.php';
        (new \CreateInvoicesTables())->up();
    }
}
