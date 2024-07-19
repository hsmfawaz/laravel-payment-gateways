<?php

namespace Hsmfawaz\PaymentGateways\Gateways\CIB;

use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentNotFoundException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class CIBGetPayment
{
    public function handle(string $ref)
    {
        $code = CIBConfig::get()->merchant_code;

        $response = $this->request()->get("merchant/$code/order/$ref");
        if (! $response->ok() || filled($response->json('error'))) {
            throw new PaymentNotFoundException(
                $response->json('error.explanation', 'Cant fetch payment ref : '.$ref)
            );
        }

        return $this->toPaidPayment(CIBPayment::fromRequest($response->json()));
    }

    private function request(): PendingRequest
    {
        $config = CIBConfig::get();
        $auth = base64_encode("merchant.{$config->merchant_code}:{$config->security_key}");

        return Http::asJson()
            ->asJson()
            ->acceptJson()
            ->withToken($auth, 'Basic')
            ->baseUrl($config->base_url);
    }

    private function toPaidPayment(CIBPayment $payment): PaidPayment
    {
        return new PaidPayment(
            ref: $payment->ref,
            gateway: GatewaysEnum::CIB,
            amount: $payment->amount,
            currency: $payment->currency,
            status: $payment->status,
            customer: $payment->customer,
            payment: $payment,
        );
    }
}