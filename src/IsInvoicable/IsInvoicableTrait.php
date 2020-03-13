<?php

namespace SanderVanHooft\Invoicable\IsInvoicable;

use SanderVanHooft\Invoicable\Bill;
use SanderVanHooft\Invoicable\Invoice;

trait IsInvoicableTrait
{
    /**
     * Set the polymorphic relation.
     *
     * @return mixed
     */
    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'invoicable');
    }

    /**
     * @return mixed
     */
    public function bills()
    {
        return $this->morphMany(Bill::class, 'invoicable');
    }
}
