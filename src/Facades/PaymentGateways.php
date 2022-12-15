<?php

namespace Hsmfawaz\PaymentGateways\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hsmfawaz\PaymentGateways\PaymentGateways
 */
class PaymentGateways extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hsmfawaz\PaymentGateways\PaymentGateways::class;
    }
}
