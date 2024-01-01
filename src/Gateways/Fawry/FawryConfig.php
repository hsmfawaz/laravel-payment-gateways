<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Fawry;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;

class FawryConfig
{
    public string $merchant_code;

    public string $security_key;

    public string $base_url;

    public string $default_currency;

    public bool $live = false;

    private static self|null $instance = null;

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
        $config = gateways_config(GatewaysEnum::FAWRY);
        if (blank($config['merchant_code']) || blank($config['security_key'])) {
            throw new \RuntimeException("Payment Gateway: Fawry is missing configuration keys");
        }
        $this->default_currency = $config['default_currency'];
        $this->merchant_code = $config['merchant_code'];
        $this->security_key = $config['security_key'];
        $this->live = (bool) $config['live'];
        $this->base_url = $this->live ? $config['live_url'] : $config['sandbox_url'];
    }
}