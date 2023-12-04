<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Stripe;

use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Enum\PaymentCurrency;
use Stripe\PaymentIntent;

class StripePaymentIntent
{
    public function __construct(public PendingPayment $payment)
    {
    }

    public function create()
    {
        return PaymentIntent::create([
            'amount' => $this->payment->totalAmount() * PaymentCurrency::centsMultiplier($this->payment->currency),
            'currency' => strtolower($this->payment->currency),
            'payment_method_types' => ['card'],
            'metadata' => [
                'ref' => $this->payment->ref,
            ],
        ]);
    }

    public function toResponse()
    {
        $paymentIntent = $this->create();

        return [
            'client_secret' => $paymentIntent->client_secret,
            'payment_method' => $paymentIntent->payment_method,
            'payment_intent' => $paymentIntent->id,
        ];
    }
}