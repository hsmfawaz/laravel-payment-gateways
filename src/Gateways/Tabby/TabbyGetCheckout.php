<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Tabby;

use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Http;

class TabbyGetCheckout
{
    public function handle(string $ref)
    {
        $response = Http::withToken(TabbyConfig::get()->security_key)
            ->asJson()
            ->acceptJson()
            ->get("https://api.tabby.ai/api/v2/checkout/{$ref}");

        if (! $response->ok() || filled($response->json('error')) || blank($response->json('payment'))) {
            throw new PaymentGatewayException(
                $response->json('error', 'Tabby: Cant fetch Payment '.$ref)
            );
        }

        return $this->toPaidPayment(TabbyPayment::fromRequest($response->json()));
    }

    private function toPaidPayment(TabbyPayment $payment): PaidPayment
    {
        return new PaidPayment(
            ref: $payment->ref,
            gateway: GatewaysEnum::TABBY,
            amount: $payment->amount,
            currency: $payment->currency,
            status: $payment->status,
            customer: $payment->customer,
            payment: $payment,
        );
    }
}