<?php


namespace SanderVanHooft\Invoicable;

use Illuminate\Support\Str;
use SanderVanHooft\Invoicable\Invoice as BaseInvoice;

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

        static::creating(function ($model) {
            /**
             * @var \Illuminate\Database\Eloquent\Model $model
             */
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
            $model->is_bill = true;
            $model->currency = config('invoicable.default_currency', 'EUR');
            $model->status = config('invoicable.default_status', 'concept');
            $model->reference = InvoiceReferenceGenerator::generate();
        });
        static::addGlobalScope(function ($query) {
            $query
                ->where('is_bill', true);
        });

    }
}
