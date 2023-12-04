<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Fawry;

use Illuminate\Support\Facades\Http;

abstract class FawrySetup
{
    public FawryConfig $config;

    public function __construct()
    {
        $this->config = FawryConfig::get();
    }

    protected function request()
    {
        return Http::baseUrl($this->config->base_url);
    }

    protected function signature(string $content)
    {
        return hash('sha256', $this->config->merchant_code.$content.$this->config->security_key);
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