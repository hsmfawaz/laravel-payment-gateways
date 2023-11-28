<?php

namespace Hsmfawaz\PaymentGateways\Facades;

use Hsmfawaz\PaymentGateways\Gateways\AmazonPay\AmazonGateway;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryGateway;
use Hsmfawaz\PaymentGateways\PaymentGateways;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getRef(string|Model $identifier)
 * @method static FawryGateway fawry()
 * @method static AmazonGateway amazon()
 * @see \Hsmfawaz\PaymentGateways\PaymentGateways
 */
class PaymentGatewaysFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PaymentGateways::class;
    }
}
