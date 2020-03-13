<?php

namespace SanderVanHooft\Invoicable\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use SanderVanHooft\Invoicable\AbstractTestCase;
use SanderVanHooft\Invoicable\Bill;
use SanderVanHooft\Invoicable\CustomerTestModel;
use SanderVanHooft\Invoicable\ProductTestModel;

class BillTest extends AbstractTestCase
{
    use DatabaseMigrations;

    private $bill;
    private $productModel;
    private $customerModel;
    private $billModel;

    public function setUp(): void
    {
        parent::setUp();
        $this->customerModel = new CustomerTestModel();
        $this->customerModel->save();
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $this->productModel = new ProductTestModel();
        $this->productModel->save();
    }

    /** @test */
    public function canCreateInvoice()
    {
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $this->assertEquals("0", (string)$this->bill->total);
        $this->assertEquals("0", (string)$this->bill->tax);
        $this->assertEquals("EUR", $this->bill->currency);
        $this->assertEquals("concept", $this->bill->status);
        $this->assertNotNull($this->bill->reference);
    }

    /** @test */
    public function canAddAmountExclTaxToInvoice()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $this->bill->addAmountExclTax(100, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->bill->addAmountExclTax(100, 'Some description', 0.21, $invoicable_id, $invoicable_type);

        $this->assertEquals("242", (string)$this->bill->total);
        $this->assertEquals("42", (string)$this->bill->tax);
    }

    /** @test */
    public function canAddAmountInclTaxToInvoice()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $this->bill->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->bill->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);

        $this->assertEquals("242", (string)$this->bill->total);
        $this->assertEquals("42", (string)$this->bill->tax);
    }

    /** @test */
    public function canHandleNegativeAmounts()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $this->bill->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->bill->addAmountInclTax(-121, 'Some negative amount description', 0.21, $invoicable_id, $invoicable_type);

        $this->assertEquals("0", (string)$this->bill->total);
        $this->assertEquals("0", (string)$this->bill->tax);
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
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);

        $this->bill->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->bill->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $view = $this->bill->view();
        $rendered = $view->render(); // fails if view cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canGetInvoicePdf()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);

        $this->bill->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->bill->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $pdf = $this->bill->pdf();  // fails if pdf cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canDownloadInvoicePdf()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);

        $this->bill->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $this->bill->addAmountInclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);
        $download = $this->bill->download(); // fails if pdf cannot be rendered
        $this->assertTrue(true);
    }

    /** @test */
    public function canFindByReference()
    {
        $bill = $this->customerModel->bills()->create([]);
        $this->assertEquals($bill->id, Bill::findByReference($bill->reference)->id);
    }

    /** @test */
    public function canFindByReferenceOrFail()
    {
        $bill = $this->customerModel->bills()->create([]);
        $this->assertEquals($bill->id, Bill::findByReferenceOrFail($bill->reference)->id);
    }

    /** @test */
    public function canFindByReferenceOrFailThrowsExceptionForNonExistingReference()
    {
        $this->expectException('Illuminate\Database\Eloquent\ModelNotFoundException');
        Bill::findByReferenceOrFail('non-existing-reference');
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
    public function ifIsFreeEqualToTrueShouldBeAmountEqualToZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $this->bill->addAmountExclTaxWithAllValues(0, 'Some description', 0, $invoicable_id, $invoicable_type, true, false);
        $this->bill->addAmountExclTaxWithAllValues(121, 'Some description', 0.21, $invoicable_id, $invoicable_type, false, false);

        $this->assertEquals(0, $this->bill->lines()->first()->amount);
    }

    /**
     * @test
     */
    public function ifIsFreeEqualToFalseShouldBeAmountGreaterThanZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $this->bill->addAmountExclTax(0, 'Some description', 0, $invoicable_id, $invoicable_type);
        $this->bill->addAmountExclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);

        $this->assertGreaterThan(0, $this->bill->lines->last()->amount);
    }

    /**
     * @test
     */
    public function ifIsComplimentaryEqualToTrueShouldBeAmountEqualToZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $this->bill->addAmountExclTaxWithAllValues(0, 'Some description', 0, $invoicable_id, $invoicable_type,false, true);
        $this->bill->addAmountExclTaxWithAllValues(121, 'Some description', 0.21, $invoicable_id, $invoicable_type, false, false);

        $this->assertEquals(0, $this->bill->lines()->first()->amount);
    }

    /**
     * @test
     */
    public function ifIsComplimentaryEqualToFalseShouldBeAmountGreaterThanZero()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->bill = $this->customerModel->bills()->create([])->fresh();

        $this->bill->addAmountExclTax(0, 'Some description', 0, $invoicable_id, $invoicable_type);
        $this->bill->addAmountExclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);

        $this->assertGreaterThan(0, $this->bill->lines->last()->amount);
    }

    /**
     * @test
     */
    public function ifIsBillEqualToTrueShouldBeReturnSumBills()
    {
        $invoicable_id = $this->productModel->id;
        $invoicable_type = get_class($this->productModel);
        $this->billModel = $this->customerModel->bills()->create([])->fresh();

        $this->billModel->addAmountExclTax(121, 'Some description', 0, $invoicable_id, $invoicable_type);
        $this->billModel->addAmountExclTax(121, 'Some description', 0.21, $invoicable_id, $invoicable_type);


        $this->assertGreaterThan(0, $this->billModel->lines->last()->amount);
    }
}
