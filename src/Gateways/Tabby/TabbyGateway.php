<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Tabby;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;

class TabbyGateway implements Gateway
{
    public function get(string $ref): ?PaidPayment
    {
        return (new TabbyGetPayment())->handle($ref);
    }

    public function create(PendingPayment $payment): NewPayment
    {
        return new TabbyNewPayment($payment);
    }
}