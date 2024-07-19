<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Stripe;

use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;

class StripeGetPaymentIntent
{
    public function handle(string $intentID): PaidPayment
    {
        $payment = PaymentIntent::retrieve($intentID);

        return $this->toPaidPayment(StripePayment::fromIntent($payment));
    }

    private function toPaidPayment(StripePayment $payment): PaidPayment
    {
        return new PaidPayment(
            ref: $payment->ref,
            gateway: GatewaysEnum::STRIPE,
            amount: $payment->amount,
            currency: $payment->currency,
            status: $payment->status,
            customer: $payment->customer,
            payment: $payment,
        );
    }
}