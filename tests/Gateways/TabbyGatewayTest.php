<?php

use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Facades\PaymentGatewaysFacade;
use Illuminate\Support\Str;

it('can initiate a new payment', function () {
    $pendingPayment = new PendingPayment(
        ref: PaymentGatewaysFacade::getRef(Str::random(3)),
        preferred_language: 'en',
        customer_email:"otp.rejected@tabby.ai",
        customer_phone: '500000001',
        customer_name: fake()->name,
        currency: 'AED',
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
    $result = PaymentGatewaysFacade::tabby()->create($pendingPayment)->toResponse();
    expect($result)->not()->toBeEmpty();

});
//
it('fetch payment', function () {
    $result = PaymentGatewaysFacade::tabby()->get("b8c2a4b5-fd65-4665-b36f-5df5b732403b");
});