<?php

namespace Hsmfawaz\PaymentGateways\Gateways\MyFatoorah;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;

class MyFatoorahGateway implements Gateway
{
    public function session(string $id): MyFatoorahSession
    {
        return (new MyFatoorahSession($id));
    }

    public function initPayment(float $amount, ?string $currency = null): MyFatoorahInitPayment
    {
        return (new MyFatoorahInitPayment($amount, $currency));
    }


    public function get(string $ref): ?PaidPayment
    {
        return (new MyFatoorahGetPayment())->handle($ref);
    }

    public function create(PendingPayment $payment): NewPayment
    {
        return (new MyFatoorahExecutePayment($payment));
    }
}