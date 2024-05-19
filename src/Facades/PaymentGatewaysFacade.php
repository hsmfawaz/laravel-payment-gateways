<?php

namespace Hsmfawaz\PaymentGateways\Facades;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Gateways\AmazonPay\AmazonGateway;
use Hsmfawaz\PaymentGateways\Gateways\CIB\CIBGateway;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryGateway;
use Hsmfawaz\PaymentGateways\Gateways\Paymob\PaymobGateway;
use Hsmfawaz\PaymentGateways\Gateways\Stripe\StripeGateway;
use Hsmfawaz\PaymentGateways\Gateways\Tabby\TabbyGateway;
use Hsmfawaz\PaymentGateways\Gateways\Tamara\TamaraGateway;
use Hsmfawaz\PaymentGateways\PaymentGateways;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getRef(string|Model $identifier)
 * @method static FawryGateway fawry()
 * @method static StripeGateway stripe()
 * @method static AmazonGateway amazon()
 * @method static TamaraGateway tamara()
 * @method static TabbyGateway tabby()
 * @method static CIBGateway cib()
 * @method static PaymobGateway paymob()
 * @method static Gateway gateway(string $paymentMethod = '')
 * @see \Hsmfawaz\PaymentGateways\PaymentGateways
 */
class PaymentGatewaysFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PaymentGateways::class;
    }
}
