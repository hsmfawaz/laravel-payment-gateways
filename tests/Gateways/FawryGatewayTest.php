<?php

use Hsmfawaz\PaymentGateways\Facades\PaymentGatewaysFacade;
use Hsmfawaz\PaymentGateways\PendingPayment;
use Illuminate\Support\Str;

it('can get a payment', function () {
    $fawry = PaymentGatewaysFacade::fawry()->get('eWg|random|1671514464.7811');
    expect($fawry->paid())->toBeTrue();
    expect($fawry->merchant_ref_number)->toBe('eWg|random|1671514464.7811');
});

it('can initiate a new payment', function () {
    $pendingPayment = new PendingPayment(
        ref: PaymentGatewaysFacade::getRef(Str::random(3)),
        preferred_language: 'en',
        customer_email: fake()->email,
        customer_phone: '+201008448891',
        customer_name: fake()->name,
        return_url: 'http://itrainer.codebase.com/redirect/payment',
        currency: 'EGP',
        description: 'Test package',
        expire_after: 24 * 60,
        items: [
            [
                'id'       => 1,
                'quantity' => 2,
                'price'    => 100.50
            ]
        ],
    );
    $result = PaymentGatewaysFacade::fawry()->create($pendingPayment)->paymentUrl();
    expect($result)->not()->toBeEmpty();
});
