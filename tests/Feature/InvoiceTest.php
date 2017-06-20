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
        $this->invoice = $this->testModel->invoices()->create([])->fresh();
    }

    /** @test */
    public function canCreateInvoice()
    {
        $this->invoice = $this->testModel->invoices()->create([])->fresh();

        $this->assertEquals("0", (string) $this->invoice->total);
        $this->assertEquals("0", (string) $this->invoice->tax);
        $this->assertEquals("EUR", $this->invoice->currency);
        $this->assertEquals("concept", $this->invoice->status);
        $this->assertNotNull($this->invoice->reference);
    }

    /** @test */
    public function canAddAmountExclTaxToInvoice()
    {
        $this->invoice = $this->testModel->invoices()->create([])->fresh();
        
        $this->invoice->addAmountExclTax(100, 'Some description', 0.21);
        $this->invoice->addAmountExclTax(100, 'Some description', 0.21);

        $this->assertEquals("242", (string) $this->invoice->total);
        $this->assertEquals("42", (string) $this->invoice->tax);
    }

    /** @test */
    public function canAddAmountInclTaxToInvoice()
    {
        $this->invoice = $this->testModel->invoices()->create([])->fresh();
        
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21);
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21);

        $this->assertEquals("242", (string) $this->invoice->total);
        $this->assertEquals("42", (string) $this->invoice->tax);
    }

    /** @test */
    public function hasUniqueReference()
    {

        $references = array_map(function () {
            return $this->testModel->invoices()->create([])->reference;
        }, range(1, 100));
        
        $this->assertCount(100, array_unique($references));
    }
}
