<?php

namespace SanderVanHooft\Invoicable\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use SanderVanHooft\Invoicable\AbstractTestCase;
use SanderVanHooft\Invoicable\CustomerTestModel;
use SanderVanHooft\Invoicable\Invoice;
use SanderVanHooft\Invoicable\ProductTestModel;

class InvoiceTest extends AbstractTestCase
{
    use DatabaseMigrations;

    private $invoice;
    private $productModel;
    private $customerModel;
    private $billModel;

    public function setUp(): void
    {
        parent::setUp();
        $this->customerModel = new CustomerTestModel();
        $this->customerModel->save();
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $this->productModel = new ProductTestModel();
        $this->productModel->save();
    }

    /** @test */
    public function canCreateInvoice()
    {
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $this->assertEquals("0", (string)$this->invoice->total);
        $this->assertEquals("0", (string)$this->invoice->tax);
        $this->assertEquals("EUR", $this->invoice->currency);
        $this->assertEquals("concept", $this->invoice->status);
        $this->assertNotNull($this->invoice->reference);
    }

    /** @test */
    public function canAddAmountExclTaxToInvoice()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $this->invoice->addAmountExclTax(100, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->invoice->addAmountExclTax(100, 'Some description', 0.21, $invoicable_id, $invoicable_type);

        $this->assertEquals("242", (string)$this->invoice->total);
        $this->assertEquals("42", (string)$this->invoice->tax);
    }

    /** @test */
    public function canAddAmountInclTaxToInvoice()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $this->invoice->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);

        $this->assertEquals("242", (string)$this->invoice->total);
        $this->assertEquals("42", (string)$this->invoice->tax);
    }

    /** @test */
    public function canHandleNegativeAmounts()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $this->invoice->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->invoice->addAmountInclTax(-121, 'Some negative amount description', 0.21, $invoicable_id, $invoicable_type);

        $this->assertEquals("0", (string)$this->invoice->total);
        $this->assertEquals("0", (string)$this->invoice->tax);
    }

    /** @test */
    public function hasUniqueReference()
    {
        $references = array_map(function () {
            return $this->customerModel->invoices()->create([])->reference;
        }, range(1, 100));

        $this->assertCount(100, array_unique($references));
    }

    /** @test */
    public function canGetInvoiceView()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);

        $this->invoice->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $view = $this->invoice->view();
        $rendered = $view->render(); // fails if view cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canGetInvoicePdf()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);

        $this->invoice->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $pdf = $this->invoice->pdf();  // fails if pdf cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canDownloadInvoicePdf()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);

        $this->invoice->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->invoice->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $download = $this->invoice->download(); // fails if pdf cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canFindByReference()
    {
        $invoice = $this->customerModel->invoices()->create([]);
        $this->assertEquals($invoice->id, Invoice::findByReference($invoice->reference)->id);
    }

    /** @test */
    public function canFindByReferenceOrFail()
    {
        $invoice = $this->customerModel->invoices()->create([]);
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
        $this->assertEquals(CustomerTestModel::class, $this->invoice->invoicable_type);
        $this->assertEquals($this->customerModel->id, $this->invoice->invoicable_id);

        // Check if invoicable is accessible
        $this->assertNotNull($this->invoice->invoicable);
        $this->assertEquals(CustomerTestModel::class, get_class($this->invoice->invoicable));
        $this->assertEquals($this->customerModel->id, $this->invoice->invoicable->id);
    }

    /**
     * @test
     */
    public function IfIsFreeEqualToTrueShouldBeAmountEqualToZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $this->invoice->addAmountExclTaxWithAllValues(0, 'Some description', 0, $invoicable_id, $invoicable_type, true, false);
        $this->invoice->addAmountExclTaxWithAllValues(121, 'Some description', 0.21, $invoicable_id, $invoicable_type, false, false);

        $this->assertEquals(0, $this->invoice->lines()->first()->amount);

    }

    /**
     * @test
     */
    public function IfIsFreeEqualToFalseShouldBeAmountGreaterThanZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $this->invoice->addAmountExclTax(0, 'Some description', 0, $invoicable_id, $invoicable_type);
        $this->invoice->addAmountExclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);

        $this->assertGreaterThan(0, $this->invoice->lines->last()->amount);

    }

    /**
     * @test
     */
    public function IfIsComplimentaryEqualToTrueShouldBeAmountEqualToZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $this->invoice->addAmountExclTaxWithAllValues(0, 'Some description', 0, $invoicable_id, $invoicable_type,false, true);
        $this->invoice->addAmountExclTaxWithAllValues(121, 'Some description', 0.21, $invoicable_id, $invoicable_type, false, false);

        $this->assertEquals(0, $this->invoice->lines()->first()->amount);

    }

    /**
     * @test
     */
    public function IfIsComplimentaryEqualToFalseShouldBeAmountGreaterThanZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $this->invoice->addAmountExclTax(0, 'Some description', 0, $invoicable_id, $invoicable_type);
        $this->invoice->addAmountExclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);

        $this->assertGreaterThan(0, $this->invoice->lines->last()->amount);

    }

}
