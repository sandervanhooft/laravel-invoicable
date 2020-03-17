<?php

namespace NeptuneSoftware\Invoicable\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use NeptuneSoftware\Invoicable\AbstractTestCase;
use NeptuneSoftware\Invoicable\CustomerTestModel;
use NeptuneSoftware\Invoicable\ProductTestModel;
use NeptuneSoftware\Invoicable\Services\InvoiceService;

class InvoiceTest extends AbstractTestCase
{
    use DatabaseMigrations;

    private $invoice;
    private $productModel;
    private $customerModel;
    private $service;


    public function setUp(): void
    {
        parent::setUp();
        $this->customerModel = new CustomerTestModel();
        $this->customerModel->save();
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();
        $this->productModel = new ProductTestModel();
        $this->productModel->save();

        $this->service = new InvoiceService($this->invoice);
    }


    /** @test */
    public function canCreateInvoice()
    {
        $this->assertEquals("0", (string)$this->invoice->total);
        $this->assertEquals("0", (string)$this->invoice->tax);
        $this->assertEquals("EUR", $this->invoice->currency);
        $this->assertEquals("concept", $this->invoice->status);
        $this->assertNotNull($this->invoice->reference);
    }

    /** @test */
    public function canAddAmountExclTaxToInvoice()
    {
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $invoice = $this->service->setReference($this->productModel)->addAmountExclTax(100, 'Some description', 0.21);
        $invoice = $this->service->setReference($this->productModel)->addAmountExclTax(100, 'Some description', 0.21);

        $this->assertEquals("242", (string)$invoice->total);
        $this->assertEquals("42", (string)$invoice->tax);
    }

    /** @test */
    public function canAddAmountInclTaxToInvoice()
    {
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $invoice = $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);
        $invoice = $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);

        $this->assertEquals("242", (string)$invoice->total);
        $this->assertEquals("42", (string)$invoice->tax);
    }

    /** @test */
    public function canHandleNegativeAmounts()
    {
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $invoice = $this->service->setReference($this->productModel)
            ->addAmountInclTax(121, 'Some description', 0.21);
        $invoice = $this->service->setReference($this->productModel)
            ->addAmountInclTax(-121, 'Some negative amount description', 0.21);

        $this->assertEquals("0", (string)$invoice->total);
        $this->assertEquals("0", (string)$invoice->tax);
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
        $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);
        $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);
        $view = $this->service->view();
        $rendered = $view->render(); // fails if view cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canGetInvoicePdf()
    {
        $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);
        $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);
        $pdf = $this->service->pdf();  // fails if pdf cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canDownloadInvoicePdf()
    {
        $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);
        $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);
        $download = $this->service->download(); // fails if pdf cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canFindByReference()
    {
        $this->assertEquals($this->invoice->id, $this->service->findByReference($this->invoice->reference)->id);
    }

    /** @test */
    public function canFindByReferenceOrFail()
    {
        $this->assertEquals($this->invoice->id, $this->service->findByReferenceOrFail($this->invoice->reference)->id);
    }

    /** @test */
    public function canFindByReferenceOrFailThrowsExceptionForNonExistingReference()
    {
        $this->expectException('Illuminate\Database\Eloquent\ModelNotFoundException');
        $this->service->findByReferenceOrFail('non-existing-reference');
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
    /*
     *
    public function ifIsFreeEqualToTrueShouldBeAmountEqualToZero()
    {
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $invoice = $this->service->setReference($this->productModel)->addAmountExclTaxWithAllValues(0, 'Some description', 0);
        $invoice = $this->service->setReference($this->productModel)->addAmountExclTaxWithAllValues(121, 'Some description', 0.21);
        $this->assertEquals(0, $invoice->lines()->first()->amount);
        $this->assertGreaterThan(0, $invoice->lines->last()->amount);
    }
    */

    /**
     * @test
     */
    /*
    public function ifIsFreeEqualToFalseShouldBeAmountGreaterThanZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $invoice = $this->service->addAmountExclTax(0, 'Some description', $invoicable_id, $invoicable_type, 0);
        $invoice = $this->service->addAmountExclTax(121, 'Some description', $invoicable_id, $invoicable_type, 0.21);

        $this->assertGreaterThan(0, $invoice->lines->last()->amount);
    }
    */

    /**
     * @test
     */
    /*
    public function ifIsComplimentaryEqualToTrueShouldBeAmountEqualToZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $invoice = $this->service->addAmountExclTaxWithAllValues(
            0,
            'Some description',
            $invoicable_id,
            $invoicable_type,
            false,
            true,
            0
        );
        $invoice = $this->service->addAmountExclTaxWithAllValues(
            121,
            'Some description',
            $invoicable_id,
            $invoicable_type,
            false,
            false,
            0.21
        );

        $this->assertEquals(0, $invoice->lines()->first()->amount);
    }
    */

    /**
     * @test
     */
    /*
    public function ifIsComplimentaryEqualToFalseShouldBeAmountGreaterThanZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->invoice = $this->customerModel->invoices()->create([])->fresh();

        $invoice = $this->service->addAmountExclTax(0, 'Some description', $invoicable_id, $invoicable_type, 0);
        $invoice = $this->service->addAmountExclTax(121, 'Some description', $invoicable_id, $invoicable_type, 0.21);

        $this->assertGreaterThan(0, $invoice->lines->last()->amount);
    }
    */
}
