<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Fawry;

use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PaymentCustomer;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentNotFoundException;
use Illuminate\Support\Facades\Http;

class FawryGetPayment
{
    public function handle(string $ref)
    {
        $response = $this->request($ref);
        if (! $response->ok() || $response->json('code') !== null) {
            throw new PaymentNotFoundException(
                $response->json('description', 'Cant fetch payment ref : '.$ref)
            );
        }

        return $this->toPaidPayment(FawryPayment::fromRequest($response->json()));
    }

    private function toPaidPayment(FawryPayment $payment): PaidPayment
    {
        return new PaidPayment(
            ref: $payment->merchant_ref_number,
            gateway: GatewaysEnum::FAWRY,
            amount: $payment->payment_amount,
            currency: FawryConfig::get()->default_currency,
            status: $payment->status,
            customer: new PaymentCustomer(
                name: $payment->customer_name ?? '',
                phone: $payment->customer_mobile ?? '',
                email: $payment->customer_mail ?? '',
            ),
            payment: $payment,
        );
    }

    protected function signature(string $content)
    {
        $config = FawryConfig::get();

        return hash('sha256', $config->merchant_code.$content.$config->security_key);
    }

    private function request(string $ref)
    {
        $config = FawryConfig::get();

        return Http::baseUrl($config->base_url)
            ->get('payments/status/v2', http_build_query([
                'merchantCode' => $config->merchant_code,
                'merchantRefNumber' => $ref,
                'signature' => $this->signature($ref),
            ]));
    }
}