<?php

namespace SanderVanHooft\Invoicable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use SanderVanHooft\Invoicable\InvoiceLine;

class Invoice extends Model
{
    protected $guarded = [];

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /**
     * Use this if the amount does not yet include tax.
     */
    public function addAmountExclTax($amount, $description, $taxPercentage = 0)
    {
        $tax = $amount * $taxPercentage;
        $this->lines()->create([
            'amount' => $amount + $tax,
            'description' => $description,
            'tax' => $tax,
            'tax_percentage' => $taxPercentage,
        ]);
        return $this->recalculate();
    }

    /**
     * Use this if the amount already includes tax.
     */
    public function addAmountInclTax($amount, $description, $taxPercentage = 0)
    {
        $this->lines()->create([
            'amount' => $amount,
            'description' => $description,
            'tax' => $amount - $amount / ( 1 + $taxPercentage ),
            'tax_percentage' => $taxPercentage,
        ]);
        return $this->recalculate();
    }

    public function recalculate()
    {
        $this->total = $this->lines()->sum('amount');
        $this->tax = $this->lines()->sum('tax');
        $this->save();
        return $this;
    }


    /**
     * Get the View instance for the invoice.
     *
     * @param  array  $data
     * @return \Illuminate\View\View
     */
    public function view(array $data = [])
    {
        return View::make('invoicable::receipt', array_merge($data, [
            'invoice' => $this,
        ]));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model)
        {
            $model->currency = config('invoicable.default_currency', 'EUR');
            $model->status = config('invoicable.default_status', 'concept');
            $model->reference = InvoiceReferenceGenerator::generate();
        });
    }
}
