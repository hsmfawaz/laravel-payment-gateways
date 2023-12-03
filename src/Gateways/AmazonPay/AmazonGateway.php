<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PaymentCustomer;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentNotFoundException;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryPayment;
use Illuminate\Support\Facades\Http;

class AmazonGateway implements Gateway
{
    public function create(PendingPayment $payment)
    {
        return new AmazonNewPayment($payment);
    }

    protected function request()
    {
        return Http::baseUrl(AmazonConfig::get()->base_url);
    }

    /**
     * @throws PaymentNotFoundException
     */
    public function get(string $ref): PaidPayment
    {
        $response = $this->request()
            ->asJson()
            ->post('', [
                    'merchantCode' => $this->merchant_code,
                    'merchantRefNumber' => $ref,
                    'signature' => $this->signature($ref),
                ]
            );
        if (! $response->ok() || $response->json('code') !== null) {
            throw new PaymentNotFoundException(
                $response->json('description', 'Cant fetch payment ref : '.$ref)
            );
        }

        return $this->toPaidPayment(AmazonPayment::fromRequest($response->json()));
    }

    private function toPaidPayment(AmazonPayment $payment): PaidPayment
    {
        return new PaidPayment(
            ref: $payment->merchant_ref_number,
            gateway: GatewaysEnum::FAWRY,
            amount: $payment->payment_amount,
            status: $payment->status,
            customer: new PaymentCustomer(
                name: $payment->customer_name,
                phone: $payment->customer_mobile,
                email: $payment->customer_mail,
            ),
            payment: $payment,
        );
    }
}