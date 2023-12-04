<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PaymentCustomer;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentNotFoundException;
use Illuminate\Support\Facades\Http;

class AmazonGetPayment
{
    public function handle(string $ref)
    {
        $url = $this->baseUrl();
        $data = $this->getData($ref);
        $response = Http::asJson()->post($url, $data);
        if (! $response->ok() || blank($response->json('transaction_status'))) {
            throw new PaymentNotFoundException(
                $response->json('description', 'Cant fetch payment ref : '.$ref)
            );
        }

        return $this->toPaidPayment(AmazonPayment::fromRequest($response->json()));
    }

    private function baseUrl(): string
    {
        return AmazonConfig::get()->live ?
            'https://paymentservices.PayFort.com/FortAPI/paymentApi' :
            'https://sbpaymentservices.PayFort.com/FortAPI/paymentApi';
    }

    private function toPaidPayment(AmazonPayment $payment): PaidPayment
    {
        return new PaidPayment(
            ref: $payment->ref,
            gateway: GatewaysEnum::AMAZON,
            amount: $payment->amount,
            status: $payment->status,
            customer: new PaymentCustomer(
                name: "",
                phone: "",
                email: "",
            ),
            payment: $payment,
        );
    }

    private function getData(string $ref)
    {
        $data = [
            'access_code' => AmazonConfig::get()->security_key,
            'merchant_identifier' => AmazonConfig::get()->merchant_code,
            'merchant_reference' => $ref,
            'language' => 'en',
            'query_command' => 'CHECK_STATUS',
        ];
        $data['signature'] = AmazonSignature::get($data);

        return $data;
    }
}