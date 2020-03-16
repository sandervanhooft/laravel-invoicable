<?php


namespace SanderVanHooft\Invoicable\Services\Interfaces;

use SanderVanHooft\Invoicable\Invoice;

interface InvoiceServiceInterface
{
    /**
     * Use this if the amount does not yet include tax.
     * @param Int $amount The amount in cents, excluding taxes
     * @param String $description The description
     * @param $invoicable_id
     * @param $invoicable_type
     * @param int $taxPercentage The tax percentage (i.e. 0.21). Defaults to 0
     * @return Invoice This instance after recalculation
     */
    public function addAmountExclTax(
        $amount,
        $description,
        $invoicable_id,
        $invoicable_type,
        $taxPercentage = 0
    ): Invoice;

    /**
     * Use this if the amount already includes tax.
     * @param Int $amount The amount in cents, including taxes
     * @param String $description The description
     * @param $invoicable_id
     * @param $invoicable_type
     * @param int $taxPercentage The tax percentage (i.e. 0.21). Defaults to 0
     * @return Invoice This instance after recalculation
     */
    public function addAmountInclTax(
        $amount,
        $description,
        $invoicable_id,
        $invoicable_type,
        $taxPercentage = 0
    ): Invoice;

    /**
     * Use this if the amount already includes tax.
     * @param Int $amount The amount in cents, including taxes
     * @param String $description The description
     * @param $invoicable_id
     * @param $invoicable_type
     * @param int $taxPercentage The tax percentage (i.e. 0.21). Defaults to 0
     * @return Invoice This instance after recalculation
     */
    public function addAmountExclTaxWithAllValues(
        $amount,
        $description,
        $invoicable_id,
        $invoicable_type,
        $is_free,
        $is_complimentary,
        $taxPercentage = 0
    ): Invoice;

    /**
     * Recalculates total and tax based on lines
     * @return Invoice This instance
     */
    public function recalculate(): Invoice;
}
