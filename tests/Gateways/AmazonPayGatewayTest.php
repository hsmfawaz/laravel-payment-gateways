<?php

use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Facades\PaymentGatewaysFacade;
use Illuminate\Support\Str;

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