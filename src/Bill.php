<?php


namespace SanderVanHooft\Invoicable;

use SanderVanHooft\Invoicable\Invoice as BaseInvoice;
use SanderVanHooft\Invoicable\InvoiceLine;

class Bill extends BaseInvoice
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoicable_id', 'invoicable_type', 'is_bill', 'price', 'discount', 'tax', 'currency',
        'reference', 'status', 'receiver_info', 'sender_info', 'payment_info', 'note'
    ];

    /**
     * Bill constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('invoicable.table_names.invoices'));
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query
                ->where('is_bill', true);
        });

        static::creating(function ($model) {
            $model->is_bill = true;
        });
    }

}
