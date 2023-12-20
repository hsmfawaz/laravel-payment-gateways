<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Tamara;

use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class TamaraNewPayment implements NewPayment
{
    public function __construct(protected PendingPayment $payment)
    {
    }

    public function toResponse(): string|array
    {
        dd($this->createSession());

        return "";
    }

    public function createSession()
    {
        $response = $this->request()->post('checkout', $this->paymentData());
        if (! $response->ok() || $response->json('code') !== null) {
            throw new PaymentGatewayException(
                $response->json('description', 'Cant initiate a new payment')
            );
        }

        return $response->body();
    }

    public function getRef(): string
    {
        return $this->payment->ref;
    }

    private function request(): PendingRequest
    {
        $config = TamaraConfig::get();

        return Http::asJson()
            ->acceptJson()
            ->withToken($config->security_key)
            ->baseUrl($config->base_url);
    }

    private function paymentData()
    {
        return [
            "total_amount" => [
                "amount" => $this->payment->totalAmount(),
                "currency" => $this->payment->currency,
            ],
            "shipping_amount" => [
                "amount" => 0,
                "currency" => $this->payment->currency,
            ],
            "tax_amount" => [
                "amount" => 0,
                "currency" => $this->payment->currency,
            ],
            "order_reference_id" => $this->payment->ref,
            "items" => $this->getItems(),
            "consumer" => [
                "email" => $this->payment->customer_email,
                "first_name" => $this->payment->firstName(),
                "last_name" => $this->payment->lastName(),
                "phone_number" => $this->payment->customer_phone,
            ],
            "country_code" => "AE",
            "description" => $this->payment->description,
            "merchant_url" => [
                "cancel" => "http://example.com/#/cancel",
                "failure" => "http://example.com/#/fail",
                "success" => "http://example.com/#/success",
                "notification" => "https://example-notification.com/payments/tamaranotifications",
            ],
            "payment_type" => "PAY_BY_INSTALMENTS",
            "instalments" => 3,
            "shipping_address" => [
                "city" => "Dubai",
                "country_code" => "AE",
                "first_name" => $this->payment->firstName(),
                "last_name" => $this->payment->lastName(),
                "line1" => "3764 Al Urubah Rd",
            ],
            "is_mobile" => false,
            "locale" => $this->payment->preferred_language === 'ar' ? "ar_SA" : "en_US",
        ];
    }

    private function getItems()
    {
        return array_map(fn ($i) => [
            "name" => $this->payment->description,
            "type" => "Digital",
            "reference_id" => $i['id'],
            "sku" => "P-".$i['id'],
            "quantity" => $i['quantity'],
            "total_amount" => [
                "amount" => $i['price'],
                "currency" => $this->payment->currency,
            ],
        ], $this->payment->items);
    }
}