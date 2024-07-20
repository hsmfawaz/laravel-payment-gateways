<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Paymob;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;

class PaymobConfig
{
    public string $api_key;
    public string $public_key = '';
    public string $integration_id;
    public string $iframe;
    public int $api_version = 1;
    public string $default_currency;
    private static self|null $instance = null;

    public string $base_url = 'https://accept.paymob.com/api/';

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
        $config = gateways_config(GatewaysEnum::PAYMOB);
        if (blank($config['api_key']) || blank($config['integration_id'])) {
            throw new \RuntimeException("Payment Gateway: Paymob is missing configuration keys");
        }
        $this->base_url = $config['base_url'] ?? $this->base_url;
        $this->api_key = $config['api_key'];
        $this->public_key = $config['public_key'];
        $this->api_version = $config['api_version'] ?? 1;
        $this->integration_id = $config['integration_id'];
        $this->iframe = $config['iframe_id'];
        $this->default_currency = $config['default_currency'] ?? 'EGP';
    }
}
