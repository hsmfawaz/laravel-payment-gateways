<?php

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Enum\PaymentCurrency;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryPayment;
use Hsmfawaz\PaymentGateways\PaymentRefGenerator;

return [
    'ref_generator' => PaymentRefGenerator::class,
    'gateways'      => [
        GatewaysEnum::FAWRY => [
            'default_currency' => PaymentCurrency::EGP,
            'live'             => env('PAYMENT_FAWRY_LIVE', false),
            'live_url'         => 'https://www.atfawry.com/ECommerceWeb/Fawry',
            'sandbox_url'      => 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry',
            'merchant_code'    => env('PAYMENT_FAWRY_MERCHANT_CODE', ''),
            'security_key'     => env('PAYMENT_FAWRY_SECURITY_KEY', ''),
            'payment_model'    => FawryPayment::class
        ]
    ]
];
