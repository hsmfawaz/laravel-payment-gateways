<?php

use Hsmfawaz\PaymentGateways\Facades\PaymentGatewaysFacade;
use Hsmfawaz\PaymentGateways\PendingPayment;
use Illuminate\Support\Str;

it('can get a payment', function () {
    $payment = PaymentGatewaysFacade::fawry()->get('eWg|random|1671514464.7811');
    expect($payment->paid())->toBeTrue();
    expect($payment->merchant_ref_number)->toBe('eWg|random|1671514464.7811');
});

it('get capture token url', function () {
    $url = PaymentGatewaysFacade::fawry()
        ->captureCardToken(
            PaymentGatewaysFacade::getRef(Str::random(3)), 'http://google.com'
        );
    expect($url)->not->toBeEmpty();
});
it('get tokens list', function () {
    $tokens = PaymentGatewaysFacade::fawry()->tokensList('105425');
    //    dump($tokens);
    expect($tokens)->toBeArray()->not->toBeEmpty();
});

it('delete token', function () {
    $tokens = PaymentGatewaysFacade::fawry()->tokensList('105425');
    $result = PaymentGatewaysFacade::fawry()->deleteToken('105425', $tokens[0]?->token ?? '');
    expect($result)->toBeTrue();
});

it('can initiate a new payment', function () {
    $pendingPayment = new PendingPayment(
        ref: PaymentGatewaysFacade::getRef(Str::random(3)),
        preferred_language: 'en',
        customer_email: fake()->email,
        customer_phone: '+201008448891',
        customer_name: fake()->name,
        currency: 'EGP',
        description: 'Test package',
        items: [
            [
                'id' => 1,
                'quantity' => 2,
                'price' => 100.50,
            ],
        ],
        expire_after: 24 * 60,
        return_url: 'http://itrainer.codebase.com/redirect/payment',
    );
    $result = PaymentGatewaysFacade::amazon()->create($pendingPayment)->toForm();
    expect($result)->not()->toBeEmpty()
        ->and($result)->toContain($pendingPayment->ref);
});
it('can charge using card token and cvv', function () {
    $tokens = PaymentGatewaysFacade::fawry()->tokensList('105425');
    expect($tokens)->toBeArray()->not->toBeEmpty();
    $pendingPayment = new PendingPayment(
        ref: PaymentGatewaysFacade::getRef(Str::random(3)),
        preferred_language: 'en',
        customer_email: fake()->email,
        customer_phone: '+201008448891',
        customer_name: fake()->name,
        currency: 'EGP',
        description: 'Test package',
        items: [
            [
                'id' => 1,
                'quantity' => 2,
                'price' => 100.50,
            ],
        ],
        method: 'CARD',
        cardToken: $tokens[0]->token,
        cardCvv: '123'
    );
    $result = PaymentGatewaysFacade::fawry()->create($pendingPayment)->charge();
    expect($result)->not()->toBeEmpty();
    //todo need better testing

});
