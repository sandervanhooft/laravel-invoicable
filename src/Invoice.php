<?php

namespace SanderVanHooft\Invoicable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use SanderVanHooft\Invoicable\Scopes\InvoiceScope;

class Invoice extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoicable_id', 'invoicable_type', 'tax',  'total', 'discount', 'currency',
        'reference', 'status', 'receiver_info', 'sender_info', 'payment_info', 'note', 'is_bill'
    ];

    protected $guarded = [];

    public $incrementing = false;

    /**
     * Invoice constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('invoicable.table_names.invoices'));
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new InvoiceScope());
        static::creating(function ($model) {
            /**
             * @var \Illuminate\Database\Eloquent\Model $model
             */
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }

            $model->is_bill   = false;
            $model->currency  = config('invoicable.default_currency', 'EUR');
            $model->status    = config('invoicable.default_status', 'concept');
            $model->reference = InvoiceReferenceGenerator::generate();
        });
    }

    /**
     * Get the invoice lines for this invoice
     */
    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function invoicable()
    {
        return $this->morphTo();
    }

}
