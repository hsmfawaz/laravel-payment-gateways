<?php

namespace Hsmfawaz\PaymentGateways\Contracts;

use Hsmfawaz\PaymentGateways\PendingPayment;

interface Gateway
{
    public function get(string $ref);

    public function create(PendingPayment $payment);
}