<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Illuminate\Support\Facades\Http;

abstract class AmazonSetup
{


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

    }
}