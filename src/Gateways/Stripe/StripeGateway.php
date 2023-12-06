<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Stripe;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Stripe\Stripe;

class StripeGateway implements Gateway
{
    public function __construct()
    {
        Stripe::setApiKey(StripeConfig::get()->security_key);
    }

    public function paymentIntent(PendingPayment $payment): StripePaymentIntent
    {
        return (new StripePaymentIntent($payment));
    }

    public function get(string $ref): ?PaidPayment
    {
        $handler = match (str_starts_with($ref, 'pi_')) {
            true => (new StripeGetPaymentIntent),
            default => (new StripeGetPaymentSession)
        };

        return $handler->handle($ref);
    }

    public function create(PendingPayment $payment): StripeNewPayment
    {
        return new StripeNewPayment($payment);
    }
}