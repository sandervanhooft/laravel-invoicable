<?php
namespace NeptuneSoftware\Invoicable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NeptuneSoftware\Invoicable\Traits\InvoicableTrait;
use NeptuneSoftware\Invoicable\Traits\IsInvoicableTrait;

class CustomerTestModel extends Model
{
    use InvoicableTrait;

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
