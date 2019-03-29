<?php

namespace SanderVanHooft\Invoicable;

use Money\Currency;
use Money\Money;
use Orchestra\Testbench\TestCase as BaseTestCase;

class AbstractTestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [InvoicableServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withPackageMigrations();
    }

    protected function withPackageMigrations()
    {
        include_once __DIR__.'/CreateTestModelsTable.php';
        (new \CreateTestModelsTable())->up();

        include_once __DIR__.'/../database/migrations/2017_06_17_163005_create_invoices_tables.php';
        (new \CreateInvoicesTables())->up();
    }

    /**
     * @param $cents
     * @param \Money\Money $money
     */
    protected function assertMoneyEurCents(int $cents, Money $money)
    {
        $this->assertTrue((new Money($cents, new Currency('EUR')))->equals($money));
    }
}
