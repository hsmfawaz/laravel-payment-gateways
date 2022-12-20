<?php

return [
    'ref_generator' => \Hsmfawaz\PaymentGateways\PaymentRefGenerator::class,
    'gateways'      => [
        'fawry' => [
            'live'          => env('PAYMENT_FAWRY_LIVE', false),
            'live_url'      => 'https://www.atfawry.com/ECommerceWeb/Fawry',
            'sandbox_url'   => 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry',
            'merchant_code' => env('PAYMENT_FAWRY_MERCHANT_CODE', ''),
            'security_key'  => env('PAYMENT_FAWRY_SECURITY_KEY', ''),
            'payment_model' => \Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryPayment::class
        ]
    ]
];
