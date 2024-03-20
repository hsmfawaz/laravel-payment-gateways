<?php

namespace Hsmfawaz\PaymentGateways\Gateways\CIB;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;

class CIBConfig
{
    public string $security_key;

    public string $base_url;

    public string $merchant_code;
    public string $installment_key;

    public string $merchant_name;

    public string $merchant_logo;

    public string $merchant_website;

    public string $default_currency;

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
        $config = gateways_config(GatewaysEnum::CIB);
        if (blank($config['security_key']) || blank($config['merchant_code'])) {
            throw new \RuntimeException("Payment Gateway: CIB is missing configuration keys");
        }
        $this->default_currency = $config['default_currency'];
        $this->security_key = $config['security_key'];
        $this->merchant_code = $config['merchant_code'];
        $this->merchant_name = $config['merchant_name'];
        $this->merchant_logo = $config['merchant_logo'];
        $this->merchant_website = $config['merchant_website'];
        $this->installment_key = $config['ticket_id'];
        $this->base_url = $config['base_url'];
    }
}