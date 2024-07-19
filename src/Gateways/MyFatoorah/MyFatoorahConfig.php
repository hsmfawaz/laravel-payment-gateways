<?php

namespace Hsmfawaz\PaymentGateways\Gateways\MyFatoorah;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class MyFatoorahConfig
{
    public bool $live;
    public string $security_key;
    public string $default_currency;
    public string $country_code = 'KW';
    public static self|null $instance = null;

    public function __construct()
    {
        $this->load();
    }

    public static function get()
    {
        return self::$instance ?? (self::$instance = new self());
    }

    private function load()
    {
        $config = gateways_config(GatewaysEnum::MYFATOORAH);
        if (blank($config['security_key']) || blank($config['default_currency'])) {
            throw new \RuntimeException("Payment Gateway: MyFatoorah is missing configuration keys");
        }
        $this->default_currency = $config['default_currency'];
        $this->security_key = $config['security_key'];
        $this->live = $config['live'];
        $this->country_code = strtolower($config['country_code']);
    }

    public function apiUrl()
    {
        if (! $this->live) {
            return 'https://apitest.myfatoorah.com/';
        }

        return match ($this->country_code) {
            'sa' => 'https://api-sa.myfatoorah.com/',
            'qa' => 'https://api-qa.myfatoorah.com/',
            default => 'https://api.myfatoorah.com/',
        };
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl($this->apiUrl())->withToken($this->security_key);
    }
}