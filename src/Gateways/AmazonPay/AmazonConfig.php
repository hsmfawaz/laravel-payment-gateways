<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;

class AmazonConfig
{
    public string $merchant_code;

    public string $security_key;

    public string $request_phrase;

    public string $response_phrase;

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
        $config = gateways_config(GatewaysEnum::AMAZON);
        $this->merchant_code = $config['merchant_code'];
        $this->security_key = $config['security_key'];
        $this->request_phrase = $config['request_phrase'];
        $this->response_phrase = $config['response_phrase'];
        $this->live = (bool) $config['live'];
        if (blank($this->merchant_code) || blank($this->security_key)) {
            throw new \RuntimeException("Payment Gateway: Amazon is missing configuration keys");
        }
    }
}