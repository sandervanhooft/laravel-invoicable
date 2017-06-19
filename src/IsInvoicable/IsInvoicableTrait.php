<?php

namespace SanderVanHooft\Invoicable\IsInvoicable;

use SanderVanHooft\Invoicable\Invoice;

trait isInvoicableTrait
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
}