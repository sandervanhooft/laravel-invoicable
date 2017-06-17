<?php

namespace SanderVanHooft\Invoicable;

use Illuminate\Database\Eloquent\Model;
use SanderVanHooft\Invoicable\Invoice;

class TestModel extends Model
{
    protected $guarded = [];

    public function invoices ()
    {
        return $this->morphMany(Invoice::class, 'invoicable');
    }
}
