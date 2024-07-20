<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Paymob;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;

class PaymobGateway implements Gateway
{
    public function get(string $ref): ?PaidPayment
    {
        return (new PaymobGetPayment())->handle($ref);
    }

    public function create(PendingPayment $payment): NewPayment
    {
        return PaymobConfig::get()->api_version === 1
            ? new PaymobNewPayment($payment)
            : new PaymobNewIntentionPayment($payment);
    }
}
