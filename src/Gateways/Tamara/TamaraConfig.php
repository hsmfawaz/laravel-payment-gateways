<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Tamara;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;

class TamaraConfig
{
    public string $security_key;

    public string $base_url;

    public string $default_currency;

    public bool $live = false;

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
        $config = gateways_config(GatewaysEnum::TAMARA);
        if (blank($config['security_key'])) {
            throw new \RuntimeException("Payment Gateway: Tamara is missing configuration keys");
        }
        $this->default_currency = $config['default_currency'];
        $this->security_key = $config['security_key'];
        $this->live = (bool) $config['live'];
        $this->base_url = $this->live ? 'https://api.tamara.co' : 'https://api-sandbox.tamara.co';

    }
}