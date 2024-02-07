<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Stripe;

use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Enum\PaymentCurrency;
use Stripe\Checkout\Session;

class StripeNewPayment implements NewPayment
{
    public Session $session;

    public function __construct(protected PendingPayment $payment)
    {
    }

    public function toResponse(): string
    {
        if (! isset($this->session)) {
            $this->createSession();
        }

        return $this->session->url;
    }

    public function createSession()
    {
        $this->session = Session::create([
            'line_items' => $this->getLineItems(),
            'mode' => 'payment',
            'success_url' => $this->getRedirectUrl(),
            'cancel_url' => $this->getRedirectUrl(),
        ]);
    }

    private function getLineItems()
    {
        return collect($this->payment->items)->map(function ($item, $key) {
            return [
                'price_data' => [
                    'currency' => $this->payment->currency,
                    'product_data' => [
                        'name' => $item['title'] ?? 'Product #'.$key,
                    ],
                    'unit_amount' => floor($item['price'] * PaymentCurrency::centsMultiplier($this->payment->currency)),
                ],
                'quantity' => $item['quantity'] ?? 1,
            ];
        })->toArray();
    }

    public function getRef(): string
    {
        return $this->session->id;
    }

    private function getRedirectUrl(): string
    {
        $query = parse_url($this->payment->return_url, PHP_URL_QUERY);
        $query = (blank($query) ? '?' : '&').'session_id={CHECKOUT_SESSION_ID}';

        return $this->payment->return_url.$query;
    }
}