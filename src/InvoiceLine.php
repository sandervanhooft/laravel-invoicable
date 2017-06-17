<?php

namespace SanderVanHooft\Invoicable;

use Illuminate\Database\Eloquent\Model;
use SanderVanHooft\Invoicable\Invoice;

class InvoiceLine extends Model
{
    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
