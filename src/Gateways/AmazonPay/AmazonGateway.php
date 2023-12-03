<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PaymentCustomer;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;

class AmazonGateway implements Gateway
{
    public function create(PendingPayment $payment)
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