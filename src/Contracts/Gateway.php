<?php

namespace Hsmfawaz\PaymentGateways\Contracts;

use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;

interface Gateway
{
    public function get(string $ref): ?PaidPayment;

    public function create(PendingPayment $payment): NewPayment;
}