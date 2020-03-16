<?php


namespace SanderVanHooft\Invoicable\Services;

use SanderVanHooft\Invoicable\Invoice;
use SanderVanHooft\Invoicable\Services\Interfaces\InvoiceServiceInterface;

class InvoiceService implements InvoiceServiceInterface
{
    /**
     * @var Invoice
     */
    private $invoiceModel;

    /**
     * RoleService constructor.
     * @param Invoice $invoiceModel
     */
    public function __construct(Invoice $invoiceModel)
    {
        $this->invoiceModel = $invoiceModel;
    }

    /**
     * @inheritDoc
     */
    public function addAmountExclTax(
        $amount,
        $description,
        $invoicable_id,
        $invoicable_type,
        $taxPercentage = 0
    ): Invoice {
        $tax = $amount * $taxPercentage;

        $this->invoiceModel->lines()->create([
            'amount' => $amount + $tax,
            'description' => $description,
            'tax' => $tax,
            'tax_percentage' => $taxPercentage,
            'invoicable_id' =>  $invoicable_id,
            'invoicable_type' =>  $invoicable_type,
        ]);
        return $this->recalculate();
    }

    /**
     * @inheritDoc
     */
    public function addAmountInclTax(
        $amount,
        $description,
        $invoicable_id,
        $invoicable_type,
        $taxPercentage = 0
    ): Invoice {
        // TODO: Implement addAmountInclTax() method.
    }

    /**
     * @inheritDoc
     */
    public function addAmountExclTaxWithAllValues(
        $amount,
        $description,
        $invoicable_id,
        $invoicable_type,
        $is_free,
        $is_complimentary,
        $taxPercentage = 0
    ): Invoice {
        // TODO: Implement addAmountExclTaxWithAllValues() method.
    }

    /**
     * @inheritDoc
     */
    public function recalculate(): Invoice
    {
        $this->invoiceModel->total = $this->invoiceModel->lines()->sum('amount');
        $this->invoiceModel->tax = $this->invoiceModel->lines()->sum('tax');
        $this->invoiceModel->save();
        return $this->invoiceModel;
    }
}
