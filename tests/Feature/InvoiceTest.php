<?php

namespace SanderVanHooft\Invoicable\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use SanderVanHooft\Invoicable\AbstractTestCase;
use SanderVanHooft\Invoicable\Invoice;
use SanderVanHooft\Invoicable\TestModel;


class InvoiceTest extends AbstractTestCase
{
    use DatabaseMigrations;

    public function setUp(): void
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
    public function canHandleNegativeAmounts()
    {
        $this->invoice = $this->testModel->invoices()->create([])->fresh();

        $this->invoice->addAmountInclTax(121, 'Some description', 0.21);
        $this->invoice->addAmountInclTax(-121, 'Some negative amount description', 0.21);

        $this->assertEquals("0", (string) $this->invoice->total);
        $this->assertEquals("0", (string) $this->invoice->tax);
    }

    /** @test */
    public function hasUniqueReference()
    {
        $references = array_map(function () {
            return $this->testModel->invoices()->create([])->reference;
        }, range(1, 100));

        $this->assertCount(100, array_unique($references));
    }

    /** @test */
    public function canGetInvoiceView()
    {
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21);
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21);
        $view = $this->invoice->view();
        $rendered = $view->render(); // fails if view cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canGetInvoicePdf()
    {
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21);
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21);
        $pdf = $this->invoice->pdf();  // fails if pdf cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canDownloadInvoicePdf()
    {
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21);
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21);
        $download = $this->invoice->download(); // fails if pdf cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canFindByReference()
    {
        $invoice = $this->testModel->invoices()->create([]);
        $this->assertEquals($invoice->id, Invoice::findByReference($invoice->reference)->id);
    }

    /** @test */
    public function canFindByReferenceOrFail()
    {
        $invoice = $this->testModel->invoices()->create([]);
        $this->assertEquals($invoice->id, Invoice::findByReferenceOrFail($invoice->reference)->id);
    }

    /** @test */
    public function canFindByReferenceOrFailThrowsExceptionForNonExistingReference()
    {
        $this->expectException('Illuminate\Database\Eloquent\ModelNotFoundException');
        Invoice::findByReferenceOrFail('non-existing-reference');
    }

    /** @test */
    public function canAccessInvoicable()
    {
        // Check if correctly set on invoice
        $this->assertEquals(TestModel::class, $this->invoice->invoicable_type);
        $this->assertEquals($this->testModel->id, $this->invoice->invoicable_id);

        // Check if invoicable is accessible
        $this->assertNotNull($this->invoice->invoicable);
        $this->assertEquals(TestModel::class, get_class($this->invoice->invoicable));
        $this->assertEquals($this->testModel->id, $this->invoice->invoicable->id);
    }
}
