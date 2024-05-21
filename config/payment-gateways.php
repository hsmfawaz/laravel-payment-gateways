<?php

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Enum\PaymentCurrency;
use Hsmfawaz\PaymentGateways\Gateways\AmazonPay\AmazonPayment;
use Hsmfawaz\PaymentGateways\Gateways\CIB\CIBPayment;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryPayment;
use Hsmfawaz\PaymentGateways\Gateways\MyFatoorah\MyFatoorahPayment;
use Hsmfawaz\PaymentGateways\Gateways\Stripe\StripePayment;
use Hsmfawaz\PaymentGateways\Gateways\Tabby\TabbyPayment;
use Hsmfawaz\PaymentGateways\PaymentRefGenerator;

return [
    'ref_generator' => PaymentRefGenerator::class,
    'gateways'      => [
        GatewaysEnum::FAWRY      => [
            'default_currency' => env('PAYMENT_FAWRY_CURRENCY', PaymentCurrency::EGP),
            'live'             => env('PAYMENT_FAWRY_LIVE', false),
            'live_url'         => 'https://www.atfawry.com/ECommerceWeb/Fawry',
            'sandbox_url'      => 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry',
            'merchant_code'    => env('PAYMENT_FAWRY_MERCHANT_CODE', ''),
            'security_key'     => env('PAYMENT_FAWRY_SECURITY_KEY', ''),
            'payment_model'    => FawryPayment::class,
        ],
        GatewaysEnum::AMAZON     => [
            'default_currency' => env('PAYMENT_AMAZON_CURRENCY', PaymentCurrency::EGP),
            'live'             => env('PAYMENT_AMAZON_LIVE', false),
            'merchant_code'    => env('PAYMENT_AMAZON_MERCHANT_CODE', 'spqybZZo'),
            'security_key'     => env('PAYMENT_AMAZON_SECURITY_KEY', 'ugenF1e24gcemBEvEkEY'),
            'request_phrase'   => env('PAYMENT_AMAZON_REQUEST_PHRASE', '955EDcJRono0VU.gwNFe4o*{'),
            'response_phrase'  => env('PAYMENT_AMAZON_RESPONSE_PHRASE', '09XwddzTtA/htY7mVsKnBH&{'),
            'payment_model'    => AmazonPayment::class,
        ],
        GatewaysEnum::STRIPE     => [
            'default_currency' => env('PAYMENT_STRIPE_CURRENCY', PaymentCurrency::EGP),
            'security_key'     => env('PAYMENT_STRIPE_SECURITY_KEY'),
            'public_key'       => env('PAYMENT_STRIPE_PUBLIC_KEY'),
            'webhook_key'      => env('PAYMENT_STRIPE_WEBHOOK_KEY'),
            'payment_model'    => StripePayment::class,
        ],
        GatewaysEnum::TABBY      => [
            'default_currency' => env('PAYMENT_TABBY_CURRENCY', PaymentCurrency::AED),
            'security_key'     => env('PAYMENT_TABBY_SECURITY_KEY', ''),
            'public_key'       => env('PAYMENT_TABBY_PUBLIC_KEY', ''),
            'merchant_code'    => env('PAYMENT_TABBY_MERCHANT_CODE', ''),
            'payment_model'    => TabbyPayment::class,
        ],
        GatewaysEnum::CIB        => [
            'default_currency' => env('PAYMENT_CIB_CURRENCY', PaymentCurrency::EGP),
            'base_url'         => 'https://cibpaynow.gateway.mastercard.com/api/rest/version/77',
            'merchant_code'    => env('PAYMENT_CIB_MERCHANT_CODE', ''),
            'security_key'     => env('PAYMENT_CIB_SECURITY_KEY', ''),
            'ticket_id'        => env('PAYMENT_CIB_TICKET_ID', ''),
            'payment_model'    => CIBPayment::class,
            'merchant_name'    => env('PAYMENT_CIB_MERCHANT_NAME', ''),
            'merchant_logo'    => env('PAYMENT_CIB_MERCHANT_LOGO', ''),
            'merchant_website' => env('PAYMENT_CIB_MERCHANT_WEBSITE', ''),
        ],
        GatewaysEnum::MYFATOORAH => [
            'default_currency' => env('PAYMENT_MYFATOORAH_CURRENCY', PaymentCurrency::KWD),
            'live'             => env('PAYMENT_MYFATOORAH_LIVE', false),
            'country_code'     => env('PAYMENT_MYFATOORAH_COUNTRY_CODE', 'KW'),
            'security_key'     => env('PAYMENT_MYFATOORAH_SECURITY_KEY',
                'rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL'),
            'payment_model'    => MyFatoorahPayment::class,
        ],
        GatewaysEnum::PAYMOB => [
            'api_key'          => env('PAYMENT_PAYMOB_API_KEY', ''),
            'security_key'     => env('PAYMENT_PAYMOB_SECURITY_KEY', ''),
            'public_key'       => env('PAYMENT_PAYMOB_PUBLIC_KEY', ''),
            'integration_id'   => env('PAYMENT_PAYMOB_INTEGRATION_ID', ''),
            'iframe_id'        => env('PAYMENT_PAYMOB_IFRAME_ID', ''),
        ],
    ],
];
