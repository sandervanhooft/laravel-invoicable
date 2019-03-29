<?php

namespace SanderVanHooft\Invoicable;

use Illuminate\Database\Eloquent\Model;
use Money\Currency;
use Money\Money;

class InvoiceLine extends Model
{
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * @return \Money\Money
     */
    public function amount()
    {
        return $this->asMoney($this->amount);
    }

    /**
     * @return \Money\Money
     */
    public function tax()
    {
        return $this->asMoney($this->tax);
    }

    /**
     * @param int $amount
     * @return \Money\Money
     */
    protected function asMoney(int $amount)
    {
        return new Money($amount, new Currency($this->invoice->currency));
    }
}
