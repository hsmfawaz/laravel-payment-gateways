<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\PendingPayment;

class AmazonGateway extends AmazonSetup
{
    public function create(PendingPayment $payment)
    {
        return new AmazonNewPayment($payment);
    }
}