<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Tabby;

use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class TabbyNewPayment implements NewPayment
{
    public function __construct(protected PendingPayment $payment)
    {
    }

    public function toResponse(): string|array
    {
        $session = $this->createSession();
        $status = data_get($session, 'status', 'rejected');
        $url = data_get($session, 'configuration.available_products.installments.0.web_url');

        if ($status === 'rejected' || blank($url)) {
            $message = $this->rejectionMessage($session);
            throw (new PaymentGatewayException(''))->setResponse($message);
        }

        return $url;
    }

    public function createSession()
    {
        $response = $this->request()->post('checkout', $this->paymentData());
        if (! $response->ok() || filled($response->json('error'))) {
            throw new PaymentGatewayException(
                $response->json('error', 'Cant initiate a new payment')
            );
        }

        return $response->json();
    }

    public function getRef(): string
    {
        return $this->payment->ref;
    }

    private function request(): PendingRequest
    {
        $config = TabbyConfig::get();

        return Http::asJson()
            ->acceptJson()
            ->withToken($config->public_key)
            ->baseUrl("https://api.tabby.ai/api/v2");
    }

    private function paymentData()
    {
        $buyer = array_filter([
            "email" => $this->payment->customer_email,
            "name" => $this->payment->customer_name,
            "phone" => $this->payment->customer_phone,
        ], 'filled');

        return [
            'payment' => [
                "amount" => $this->payment->totalAmount(),
                "currency" => $this->payment->currency,
                "description" => $this->payment->description,
                "buyer" => $buyer,
                "shipping_address" => [
                    "city" => "Dubai",
                    "zip" => "25314",
                    "address" => "3764 Al Urubah Rd",
                ],
                "buyer_history" => [
                    "registered_since" => now()->toIso8601String(),
                    "loyalty_level" => 0,
                ],
                "order_history" => [],
                "order" => [
                    "reference_id" => $this->payment->ref,
                    "items" => $this->getItems(),
                ],
            ],
            "merchant_urls" => [
                "cancel" => $this->payment->return_url,
                "failure" => $this->payment->return_url,
                "success" => $this->payment->return_url,
            ],
            "merchant_code" => TabbyConfig::get()->merchant_code,
            "lang" => $this->payment->preferred_language,
        ];
    }

    private function getItems()
    {
        return array_map(fn ($i) => [
            "title" => $this->payment->description,
            "category" => "Subscriptions",
            "reference_id" => (string) $i['id'],
            "quantity" => $i['quantity'],
            "unit_price" => $i['price'],
        ], $this->payment->items);
    }

    private function rejectionMessage($session): string
    {
        $reason = data_get(
            $session, 'configuration.products.installments.rejection_reason', 'not_available'
        );

        return match ($reason) {
            "not_available" => "Sorry, Tabby is unable to approve this purchase. Please use an alternative payment method for your order.",
            "order_amount_too_high" => "This purchase is above your current spending limit with Tabby, try a smaller cart or use another payment method",
            "order_amount_too_low" => "The purchase amount is below the minimum amount required to use Tabby, try adding more items or use another payment method",
            default => "Sorry, Tabby is unable to approve this purchase"
        };
    }
}