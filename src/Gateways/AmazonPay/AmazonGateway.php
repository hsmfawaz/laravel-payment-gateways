<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentNotFoundException;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryPayment;
use Hsmfawaz\PaymentGateways\PendingPayment;
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
    public function get(string $ref): AmazonPayment
    {
        $response = $this->request()
            ->asJson()
            ->post('', $this->queryParams([
                'merchantCode' => $this->merchant_code,
                'merchantRefNumber' => $ref,
                'signature' => $this->signature($ref),
            ]
        ));
        if (! $response->ok() || $response->json('code') !== null) {
            throw new PaymentNotFoundException($response->json('description',
                'Cant fetch payment ref : '.$ref));
        }

        return FawryPayment::fromRequest($response->json());
    }
}