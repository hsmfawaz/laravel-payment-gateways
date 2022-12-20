<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Fawry;

use Hsmfawaz\PaymentGateways\Exceptions\PaymentNotFoundException;
use Hsmfawaz\PaymentGateways\PendingPayment;
use Illuminate\Support\Facades\Http;

class FawryGateway
{
    protected string $merchant_code;
    protected string $security_key;
    protected string $base_url;

    public function __construct()
    {
        $this->setup();
    }

    public function create(PendingPayment $payment)
    {
        return new FawryNewPayment($payment);
    }

    /**
     * @throws PaymentNotFoundException
     */
    public function get(string $ref)
    {
        $response = $this->request()->get('payments/status/v2', $this->queryParams([
                'merchantCode'      => $this->merchant_code,
                'merchantRefNumber' => $ref,
                'signature'         => $this->signature($ref),
            ]
        ));
        if (! $response->ok() || $response->json('code') !== null) {
            throw new PaymentNotFoundException($response->json('description', 'Cant fetch payment ref : '.$ref));
        }

        return FawryPayment::fromRequest($response->json());
    }

    protected function request()
    {
        return Http::baseUrl($this->base_url);
    }

    private function setup()
    {
        $config = config('payment-gateways.gateways.fawry');
        $this->merchant_code = $config['merchant_code'];
        $this->security_key = $config['security_key'];
        $this->base_url = $config['live'] ? $config['live_url'] : $config['sandbox_url'];
        if (blank($this->merchant_code) || blank($this->security_key)) {
            throw new \RuntimeException("Payment Gateway: Fawry is missing configuration keys");
        }
    }

    private function signature(string $content)
    {
        return hash('sha256', $this->merchant_code.$content.$this->security_key);
    }

    private function queryParams(array $array): string
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[] = $key."=".$value;
        }

        return implode('&', $result);
    }
}