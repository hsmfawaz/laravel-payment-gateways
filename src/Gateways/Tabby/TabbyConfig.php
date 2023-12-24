<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Tabby;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;

class TabbyConfig
{
    public string $security_key;

    public string $public_key;

    public string $merchant_code;

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
        $config = gateways_config(GatewaysEnum::TABBY);
        if (blank($config['security_key'])) {
            throw new \RuntimeException("Payment Gateway: Tabby is missing configuration keys");
        }
        $this->default_currency = $config['default_currency'];
        $this->security_key = $config['security_key'];
        $this->public_key = $config['public_key'];
        $this->merchant_code = $config['merchant_code'];
    }
}