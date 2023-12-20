<?php

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Enum\PaymentCurrency;
use Hsmfawaz\PaymentGateways\Gateways\AmazonPay\AmazonPayment;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryPayment;
use Hsmfawaz\PaymentGateways\Gateways\Stripe\StripePayment;
use Hsmfawaz\PaymentGateways\Gateways\Tamara\TamaraPayment;
use Hsmfawaz\PaymentGateways\PaymentRefGenerator;

return [
    'ref_generator' => PaymentRefGenerator::class,
    'gateways' => [
        GatewaysEnum::FAWRY => [
            'default_currency' => env('PAYMENT_FAWRY_CURRENCY', PaymentCurrency::EGP),
            'live' => env('PAYMENT_FAWRY_LIVE', false),
            'live_url' => 'https://www.atfawry.com/ECommerceWeb/Fawry',
            'sandbox_url' => 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry',
            'merchant_code' => env('PAYMENT_FAWRY_MERCHANT_CODE', ''),
            'security_key' => env('PAYMENT_FAWRY_SECURITY_KEY', ''),
            'payment_model' => FawryPayment::class,
        ],
        GatewaysEnum::AMAZON => [
            'default_currency' => env('PAYMENT_AMAZON_CURRENCY', PaymentCurrency::EGP),
            'live' => env('PAYMENT_AMAZON_LIVE', false),
            'merchant_code' => env('PAYMENT_AMAZON_MERCHANT_CODE', 'spqybZZo'),
            'security_key' => env('PAYMENT_AMAZON_SECURITY_KEY', 'ugenF1e24gcemBEvEkEY'),
            'request_phrase' => env('PAYMENT_AMAZON_REQUEST_PHRASE', '955EDcJRono0VU.gwNFe4o*{'),
            'response_phrase' => env('PAYMENT_AMAZON_RESPONSE_PHRASE', '09XwddzTtA/htY7mVsKnBH&{'),
            'payment_model' => AmazonPayment::class,
        ],
        GatewaysEnum::STRIPE => [
            'default_currency' => env('PAYMENT_STRIPE_CURRENCY', PaymentCurrency::EGP),
            'security_key' => env('PAYMENT_STRIPE_SECURITY_KEY'),
            'public_key' => env('PAYMENT_STRIPE_PUBLIC_KEY'),
            'webhook_key' => env('PAYMENT_STRIPE_WEBHOOK_KEY'),
            'payment_model' => StripePayment::class,
        ],
        GatewaysEnum::TAMARA => [
            'default_currency' => env('PAYMENT_TAMARA_CURRENCY', PaymentCurrency::AED),
            'live' => env('PAYMENT_TAMARA_LIVE', false),
            'security_key' => env('PAYMENT_TAMARA_SECURITY_KEY'),
            'payment_model' => TamaraPayment::class,
        ],
        GatewaysEnum::TABBY => [
            'default_currency' => env('PAYMENT_TAMARA_CURRENCY', PaymentCurrency::AED),
            'live' => env('PAYMENT_TAMARA_LIVE', false),
            'security_key' => env('PAYMENT_TAMARA_SECURITY_KEY'),
            'payment_model' => TamaraPayment::class,
        ],
    ],
];
