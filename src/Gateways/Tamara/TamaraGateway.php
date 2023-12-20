<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Tamara;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;

class TamaraGateway implements Gateway
{
    public function get(string $ref): ?PaidPayment
    {
        // TODO: Implement get() method.
    }

    public function create(PendingPayment $payment): NewPayment
    {
        return new TamaraNewPayment($payment);
    }
}