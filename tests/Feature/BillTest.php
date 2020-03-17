<?php

namespace NeptuneSoftware\Invoicable\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use NeptuneSoftware\Invoicable\AbstractTestCase;
use NeptuneSoftware\Invoicable\CustomerTestModel;
use NeptuneSoftware\Invoicable\ProductTestModel;
use NeptuneSoftware\Invoicable\Services\BillService;

class BillTest extends AbstractTestCase
{
    use DatabaseMigrations;

    private $bill;
    private $productModel;
    private $customerModel;
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->customerModel = new CustomerTestModel();
        $this->customerModel->save();
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $this->productModel = new ProductTestModel();
        $this->productModel->save();

        $this->service = new BillService($this->bill);
    }

    /** @test */
    public function canCreateInvoice()
    {
        $this->assertEquals("0", (string)$this->bill->total);
        $this->assertEquals("0", (string)$this->bill->tax);
        $this->assertEquals("EUR", $this->bill->currency);
        $this->assertEquals("concept", $this->bill->status);
        $this->assertNotNull($this->bill->reference);
    }

    /** @test */
    public function canAddAmountExclTaxToInvoice()
    {
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $bill = $this->service->setReference($this->productModel)->addAmountExclTax(100, 'Some description', 0.21);
        $bill = $this->service->setReference($this->productModel)->addAmountExclTax(100, 'Some description', 0.21);

        $this->assertEquals("242", (string)$bill->total);
        $this->assertEquals("42", (string)$bill->tax);
    }

    /** @test */
    public function canAddAmountInclTaxToInvoice()
    {
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $bill = $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);
        $bill = $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);

        $this->assertEquals("242", (string)$bill->total);
        $this->assertEquals("42", (string)$bill->tax);
    }

    /** @test */
    public function canHandleNegativeAmounts()
    {
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $bill = $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);
        $bill = $this->service->setReference($this->productModel)->addAmountInclTax(-121, 'Some negative amount description', 0.21);

        $this->assertEquals("0", (string)$bill->total);
        $this->assertEquals("0", (string)$bill->tax);
    }

    /** @test */
    public function hasUniqueReference()
    {
        $references = array_map(function () {
            return $this->customerModel->bills()->create([])->reference;
        }, range(1, 100));

        $this->assertCount(100, array_unique($references));
    }

    /** @test */
    public function canGetInvoiceView()
    {
        $bill = $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);
        $bill = $this->service->setReference($this->productModel)->addAmountInclTax(121, 'Some description', 0.21);
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
        $this->assertEquals($this->bill->id, $this->service->findByReference($this->bill->reference)->id);
    }

    /** @test */
    public function canFindByReferenceOrFail()
    {
        $this->assertEquals($this->bill->id, $this->service->findByReferenceOrFail($this->bill->reference)->id);
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
        $this->assertEquals(CustomerTestModel::class, $this->bill->invoicable_type);
        $this->assertEquals($this->customerModel->id, $this->bill->invoicable_id);

        // Check if invoicable is accessible
        $this->assertNotNull($this->bill->invoicable);
        $this->assertEquals(CustomerTestModel::class, get_class($this->bill->invoicable));
        $this->assertEquals($this->customerModel->id, $this->bill->invoicable->id);
    }

    /**
     * @test
     */
    /*
    public function ifIsFreeEqualToTrueShouldBeAmountEqualToZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $bill = $this->service->addAmountExclTaxWithAllValues(
            0,
            'Some description',
            $invoicable_id,
            $invoicable_type,
            true,
            false,
            0
        );
        $bill = $this->service->addAmountExclTaxWithAllValues(
            121,
            'Some description',
            $invoicable_id,
            $invoicable_type,
            false,
            false,
            0
        );

        $this->assertEquals(0, $bill->lines()->first()->amount);
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
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $bill = $this->service->addAmountExclTax(0, 'Some description', $invoicable_id, $invoicable_type, 0);
        $bill = $this->service->addAmountExclTax(121, 'Some description', $invoicable_id, $invoicable_type, 0.21);

        $this->assertGreaterThan(0, $bill->lines->last()->amount);
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
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $bill = $this->service->addAmountExclTaxWithAllValues(
            0,
            'Some description',
            $invoicable_id,
            $invoicable_type,
            false,
            true,
            0
        );
        $bill = $this->service->addAmountExclTaxWithAllValues(
            121,
            'Some description',
            $invoicable_id,
            $invoicable_type,
            false,
            false,
            0.21
        );

        $this->assertEquals(0, $bill->lines()->first()->amount);
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
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $bill = $this->service->addAmountExclTax(0, 'Some description', $invoicable_id, $invoicable_type, 0);
        $bill = $this->service->addAmountExclTax(121, 'Some description', $invoicable_id, $invoicable_type, 0.21);

        $this->assertGreaterThan(0, $bill->lines->last()->amount);
    }
    */

    /**
     * @test
     */
    /*
    public function ifIsBillEqualToTrueShouldBeReturnSumBills()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $bill = $this->service->addAmountExclTax(
            121,
            'Some description',
            $invoicable_id,
            $invoicable_type,
            0
        );
        $bill = $this->service->addAmountExclTax(
            121,
            'Some description',
            $invoicable_id,
            $invoicable_type,
            0
        );


        $this->assertGreaterThan(0, $bill->lines->last()->amount);
    }
    */
}
