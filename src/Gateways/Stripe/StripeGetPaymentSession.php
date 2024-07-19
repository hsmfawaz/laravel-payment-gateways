<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Stripe;

use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Stripe\Checkout\Session;

class StripeGetPaymentSession
{
    public function handle(string $sessionID): PaidPayment
    {
        $session = Session::retrieve($sessionID);

        return $this->toPaidPayment(StripePayment::fromSession($session));
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