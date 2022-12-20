<?php

namespace Hsmfawaz\PaymentGateways\Facades;

use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryGateway;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getRef(string|Model $identifier)
 * @method static FawryGateway fawry()
 * @see \Hsmfawaz\PaymentGateways\PaymentGateways
 */
class PaymentGatewaysFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hsmfawaz\PaymentGateways\PaymentGateways::class;
    }
}
