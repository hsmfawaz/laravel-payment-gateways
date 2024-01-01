<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;

class AmazonGateway implements Gateway
{
    public function create(PendingPayment $payment): NewPayment
    {
        return new AmazonNewPayment($payment);
    }

    /**
     * @throws \Hsmfawaz\PaymentGateways\Exceptions\PaymentNotFoundException
     */
    public function get(string $ref): PaidPayment
    {
        return (new AmazonGetPayment())->handle($ref);
    }
}