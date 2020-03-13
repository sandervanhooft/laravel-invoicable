<?php
namespace SanderVanHooft\Invoicable;

use Illuminate\Support\Str;
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
    }

    /**
     * Get the invoice lines for this invoice
     */
    public function lines()
    {
        return $this->hasMany(InvoiceLine::class, 'invoice_id')->withoutGlobalScope(InvoiceScope::class);
    }

    public static function findByReference($reference)
    {
        return static::where('reference', $reference)->withoutGlobalScope(InvoiceScope::class)->first();
    }

    public static function findByReferenceOrFail($reference)
    {
        return static::where('reference', $reference)->withoutGlobalScope(InvoiceScope::class)->firstOrFail();
    }
}
