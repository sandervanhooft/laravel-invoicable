<?php

namespace SanderVanHooft\Invoicable\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use SanderVanHooft\Invoicable\TestModel;
use SanderVanHooft\Invoicable\AbstractTestCase;

class InvoiceTest extends AbstractTestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->testModel = new TestModel();
        $this->testModel->save();
    }

    /** @test */
    public function canCreateInvoice()
    {
        $invoice = $this->testModel->invoices()->create([])->fresh();

        $this->assertEquals("0", (string) $invoice->total);
        $this->assertEquals("0", (string) $invoice->tax);
        $this->assertEquals("EUR", $invoice->currency);
        $this->assertEquals("concept", $invoice->status);
    }

    /** @test */
    public function canAddAmountExclTaxToInvoice()
    {
        $invoice = $this->testModel->invoices()->create([])->fresh();
        
        $invoice->addAmountExclTax(100, 'Some description', 0.21);
        $invoice->addAmountExclTax(100, 'Some description', 0.21);

        $this->assertEquals("242", (string) $invoice->total);
        $this->assertEquals("42", (string) $invoice->tax);
    }

    /** @test */
    public function canAddAmountInclTaxToInvoice()
    {
        $invoice = $this->testModel->invoices()->create([])->fresh();
        
        $invoice->addAmountInclTax(121, 'Some description', 0.21);
        $invoice->addAmountInclTax(121, 'Some description', 0.21);

        $this->assertEquals("242", (string) $invoice->total);
        $this->assertEquals("42", (string) $invoice->tax);
    }
}
