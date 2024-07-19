<?php

namespace Hsmfawaz\PaymentGateways\Gateways\CIB;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;

class CIBGateway implements Gateway
{
    public function get(string $ref): ?PaidPayment
    {
        return (new CIBGetPayment())->handle($ref);
    }

    public function create(PendingPayment $payment): NewPayment
    {
        return new CIBNewPayment($payment);
    }
}