<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Paymob;

use Carbon\Carbon;
use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Http;

class PaymobNewPayment implements NewPayment
{
    private string $authToken = '';

    public function __construct(protected PendingPayment $payment)
    {
        $this->authToken = $this->getAuthToken();
    }

    public function toResponse(): string|array
    {
        $token = $this->getPaymentToken();
        return $this->paymentUrl($token);
    }

    public function getRef(): string
    {
        return $this->payment->ref;
    }

    public function paymentUrl($token): string
    {
        $iframe = PaymobConfig::get()->iframe;
        return 'https://accept.paymob.com/api/acceptance/iframes/' . $iframe . '?payment_token=' . $token['token'];
    }

    private function baseUrl()
    {
        return PaymobConfig::get()->base_url;
    }

    private function getPaymentToken()
    {
        $order = $this->createOrder();

        $response = Http::post($this->baseUrl() . '/acceptance/payment_keys', [
            'auth_token' => $this->authToken,
            'amount_cents' => ceil(($this->payment->totalAmount() * 100)),
            'expiration' => 3600, // 1 hour
            'currency' => $this->payment->currency,
            'order_id' => $order['id'],
            'integration_id' => PaymobConfig::get()->integration_id,
            "billing_data" => [
                'email' => $this->payment->customer_email,
                'first_name' => $this->payment->firstName(),
                'last_name' => $this->payment->lastName(),
                'phone_number' => $this->payment->customer_phone,
                'street' => 'NA',
                'building' => 'NA',
                'floor' => 'NA',
                'apartment' => 'NA',
                'city' => 'NA',
                'country' => 'NA',
            ],
        ]);

        if (($response->json('token') ?? null) === null) {
            throw new PaymentGatewayException($response->json('message', 'Cant get payment token'));
        }

        return $response->json();
    }

    private function getAuthToken()
    {
        $response = Http::post($this->baseUrl() . '/auth/tokens', [
            'api_key' => PaymobConfig::get()->api_key,
        ]);
        if (($response->json('token') ?? null) === null) {
            throw new PaymentGatewayException($response->json('message',
                'Cant get auth token'));
        }

        return $this->authToken = $response->json('token');
    }

    private function createOrder()
    {
        $response = Http::post($this->baseUrl() . '/ecommerce/orders', [
            'auth_token' => $this->authToken,
            'delivery_needed' => 'false',
            'amount_cents' => ceil(($this->payment->totalAmount() * 100)),
            'items' => [],
            'merchant_order_id' => $this->payment->ref,
        ]);


        if (($response->json('id') ?? null) === null) {
            throw new PaymentGatewayException($response->json('message', 'Cant create order'));
        }

        return $response->json();
    }

}
