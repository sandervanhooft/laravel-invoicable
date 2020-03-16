<?php
namespace SanderVanHooft\Invoicable;

use SanderVanHooft\Invoicable\Invoice as BaseInvoice;
use SanderVanHooft\Invoicable\Scopes\BillScope;
use SanderVanHooft\Invoicable\Scopes\InvoiceScope;

class Bill extends BaseInvoice
{

    protected $guarded = [];

    public $incrementing = false;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new BillScope());
        static::creating(function ($model) {
            $model->is_bill = true;
        });
    }

    /**
     * Get the invoice lines for this invoice
     */
    public function lines()
    {
        return $this->hasMany(InvoiceLine::class, 'invoice_id')->withoutGlobalScope(InvoiceScope::class);
    }

}
