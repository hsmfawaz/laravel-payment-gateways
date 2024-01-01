<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Fawry;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;

class FawryGateway implements Gateway
{
    public function create(PendingPayment $payment): NewPayment
    {
        return new FawryNewPayment($payment);
    }

    public function get(string $ref): PaidPayment
    {
        return (new FawryGetPayment())->handle($ref);
    }
}