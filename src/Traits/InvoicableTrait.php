<?php

namespace NeptuneSoftware\Invoicable\Traits;

use NeptuneSoftware\Invoicable\Models\Bill;
use NeptuneSoftware\Invoicable\Models\Invoice;

trait InvoicableTrait
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
