<?php

namespace Hsmfawaz\PaymentGateways\Gateways\MyFatoorah;

use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentNotFoundException;

class MyFatoorahGetPayment
{
    public function handle(string $ref)
    {
        $response = MyFatoorahConfig::get()->request()->post('v2/GetPaymentStatus', [
            'KeyType' => 'PaymentId',
            'Key'     => $ref,
        ]);
        if (! $response->ok() || ! $response->json('IsSuccess')) {
            throw new PaymentNotFoundException(
                $response->json('description', 'Cant fetch payment ref : '.$ref)
            );
        }

        return $this->toPaidPayment(MyFatoorahPayment::fromRequest($response->json('Data')));
    }

    private function toPaidPayment(MyFatoorahPayment $payment): PaidPayment
    {
        return new PaidPayment(
            ref: $payment->ref,
            gateway: GatewaysEnum::FAWRY,
            amount: $payment->amount,
            currency: $payment->currency,
            status: $payment->status,
            customer: $payment->customer,
            payment: $payment,
        );
    }
}