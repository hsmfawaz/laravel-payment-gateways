<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Stripe;

use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Stripe\Stripe;

class StripeGateway
{
    public function __construct()
    {
        Stripe::setApiKey(StripeConfig::get()->security_key);
    }

    public function createPaymentIntent(PendingPayment $payment): StripePaymentIntent
    {
        return (new StripePaymentIntent($payment));
    }
}