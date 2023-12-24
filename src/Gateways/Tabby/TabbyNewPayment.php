<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Tabby;

use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class TabbyNewPayment implements NewPayment
{
    public string $ref;

    public function __construct(protected PendingPayment $payment)
    {
    }

    public function toResponse(): string|array
    {
        $session = $this->createSession();
        $this->ref = data_get($session, 'id');
        $url = data_get($session, 'configuration.available_products.installments.0.web_url');

        if (blank($url)) {
            throw new PaymentGatewayException('Cant get installment url');
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
        return $this->ref;
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
        return [
            'payment' => [
                "amount" => $this->payment->totalAmount(),
                "currency" => $this->payment->currency,
                "description" => $this->payment->description,
                "buyer" => [
                    "email" => $this->payment->customer_email,
                    "name" => $this->payment->customer_name,
                    "phone" => $this->payment->customer_phone,
                ],
                "shipping_address" => [
                    "city" => "Dubai",
                    "zip" => "25314",
                    "address" => "3764 Al Urubah Rd",
                ],
                "order" => [
                    "reference_id" => $this->payment->ref,
                    "items" => $this->getItems(),
                ],
            ],
            "merchant_url" => [
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
}