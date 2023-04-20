<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Fawry;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Illuminate\Support\Facades\Http;

abstract class FawrySetup
{
    protected string $merchant_code;
    protected string $security_key;
    protected string $base_url;
    protected bool $live = false;

    public function __construct()
    {
        $this->setup();
    }

    protected function request()
    {
        return Http::baseUrl($this->base_url);
    }

    private function setup()
    {
        $config = config('payment-gateways.gateways.'.GatewaysEnum::FAWRY);
        $this->merchant_code = $config['merchant_code'];
        $this->security_key = $config['security_key'];
        $this->live = (bool) $config['live'];
        $this->base_url = $this->live ? $config['live_url'] : $config['sandbox_url'];
        if (blank($this->merchant_code) || blank($this->security_key)) {
            throw new \RuntimeException("Payment Gateway: Fawry is missing configuration keys");
        }
    }

    protected function signature(string $content)
    {
        return hash('sha256', $this->merchant_code.$content.$this->security_key);
    }

    protected function queryParams(array $array): string
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[] = $key."=".$value;
        }

        return implode('&', $result);
    }
}