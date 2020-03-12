<?php


namespace SanderVanHooft\Invoicable;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use SanderVanHooft\Invoicable\IsInvoicable\IsInvoicableTrait;

class CustomerTestModel extends Model
{
    use IsInvoicableTrait;

    public $incrementing = false;

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
        });
    }
}