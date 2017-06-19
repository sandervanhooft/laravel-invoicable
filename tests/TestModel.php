<?php

namespace SanderVanHooft\Invoicable;

use Illuminate\Database\Eloquent\Model;
use SanderVanHooft\Invoicable\IsInvoicable\IsInvoicableTrait;

class TestModel extends Model
{
    use IsInvoicableTrait;

    protected $guarded = [];
}
