<?php

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Enum\PaymentCurrency;
use Hsmfawaz\PaymentGateways\Gateways\AmazonPay\AmazonPayment;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryPayment;
use Hsmfawaz\PaymentGateways\PaymentRefGenerator;

return [
    'ref_generator' => PaymentRefGenerator::class,
    'gateways' => [
        GatewaysEnum::FAWRY => [
            'default_currency' => PaymentCurrency::EGP,
            'live' => env('PAYMENT_FAWRY_LIVE', false),
            'live_url' => 'https://www.atfawry.com/ECommerceWeb/Fawry',
            'sandbox_url' => 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry',
            'merchant_code' => env('PAYMENT_FAWRY_MERCHANT_CODE', ''),
            'security_key' => env('PAYMENT_FAWRY_SECURITY_KEY', ''),
            'payment_model' => FawryPayment::class,
        ],
        GatewaysEnum::AMAZON => [
            'default_currency' => PaymentCurrency::EGP,
            'live' => env('PAYMENT_AMAZON_LIVE', false),
            'live_url' => 'https://checkout.payfort.com/FortAPI',
            'sandbox_url' => 'https://sbcheckout.payfort.com/FortAPI',
            'merchant_code' => env('PAYMENT_AMAZON_MERCHANT_CODE', 'spqybZZo'),
            'security_key' => env('PAYMENT_AMAZON_SECURITY_KEY', 'ugenF1e24gcemBEvEkEY'),
            'request_phrase' => env('PAYMENT_AMAZON_REQUEST_PHRASE', '955EDcJRono0VU.gwNFe4o*{'),
            'response_phrase' => env('PAYMENT_AMAZON_RESPONSE_PHRASE', '09XwddzTtA/htY7mVsKnBH&{'),
            'payment_model' => AmazonPayment::class,
        ],
    ],
];
