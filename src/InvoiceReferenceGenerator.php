<?php

namespace NeptuneSoftware\Invoicable;

use Carbon\Carbon;

class InvoiceReferenceGenerator
{
    public static function generate()
    {
        $date = Carbon::now();
        return $date->format('Y-m-d') . '-' . self::generateRandomCode();
    }

    protected static function generateRandomCode()
    {
        $pool = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        return substr(str_shuffle(str_repeat($pool, 6)), 0, 6);
    }
}
