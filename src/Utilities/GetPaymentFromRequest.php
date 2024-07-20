<?php

namespace Hsmfawaz\PaymentGateways\Utilities;

use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Hsmfawaz\PaymentGateways\Facades\PaymentGatewaysFacade;

class GetPaymentFromRequest
{
    public function handle(string $gateway): PaidPayment
    {
        $ref = $this->getRef($gateway);

        return PaymentGatewaysFacade::gateway($gateway)->get($ref);
    }

    private function getRef($payment)
    {
        $ref = match ($payment) {
            'stripe' => $this->getStripeRef(),
            'paymob' => request('id'),
            'tabby' => $this->multiKey(['payment_id', 'id']),
            'cib' => request('ref'),
            'amazon' => request('merchant_reference'),
            default => request('merchantRefNumber'),
        };

        throw_if(blank($ref), new PaymentGatewayException('Payment: cant recognize the payment method'));

        return $ref;
    }

    private function getStripeRef()
    {
        $possibleKeys = [
            'session_id', 'data.object.id', 'payment_intent', 'data.object.payment_intent',
        ];

        return $this->multiKey($possibleKeys);
    }

    public function multiKey($keys)
    {
        foreach ($keys as $key) {
            if (request()->has($key)) {
                return request($key);
            }
        }

        return null;
    }
}