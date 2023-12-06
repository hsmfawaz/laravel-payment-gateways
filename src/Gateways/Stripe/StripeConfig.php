<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Stripe;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;

class StripeConfig
{
    public string $security_key;

    public string $public_key;

    public string $webhook_key;

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
        $config = gateways_config(GatewaysEnum::STRIPE);
        if (blank($config['security_key']) || blank($config['webhook_key'])) {
            throw new \RuntimeException("Payment Gateway: stripe is missing configuration keys");
        }
        $this->security_key = $config['security_key'];
        $this->public_key = $config['public_key'];
        $this->webhook_key = $config['webhook_key'];
        $this->default_currency = $config['default_currency'];
    }
}