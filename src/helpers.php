<?php

if (! function_exists('gateways_config')) {
    function gateways_config(string $gateway, string $key = '', $default = null)
    {
        return config(
            'payment-gateways.gateways.'.$gateway.(filled($key) ? '.'.$key : ''), $default
        );
    }
}