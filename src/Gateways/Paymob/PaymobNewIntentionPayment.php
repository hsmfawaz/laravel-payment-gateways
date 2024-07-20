<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Paymob;

use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Http;

class PaymobNewIntentionPayment implements NewPayment
{
    public function __construct(protected PendingPayment $payment)
    {
    }

    public function toResponse(): string|array
    {
        $token = $this->createIntention()['client_secret'];

        return $this->paymentUrl($token);
    }

    public function getRef(): string
    {
        return $this->payment->ref;
    }

    public function paymentUrl($token): string
    {
        $public = PaymobConfig::get()->public_key;

        return $this->baseUrl()."/unifiedcheckout/?publicKey={$public}&clientSecret={$token}";
    }

    private function baseUrl()
    {
        return PaymobConfig::get()->base_url;
    }

    private function createIntention()
    {
        $response = Http::withToken(PaymobConfig::get()->api_key, 'Token')
                        ->asJson()
                        ->withHeader('Accept-Language', $this->payment->preferred_language)
                        ->post($this->baseUrl().'v1/intention/', [
                            'amount' => ceil(($this->payment->totalAmount() * 100)),
                            'currency' => $this->payment->currency,
                            'special_reference' => $this->payment->ref,
                            'payment_methods' => [PaymobConfig::get()->integration_id],
                            'customer' => [
                                'email' => filled($this->payment->customer_email) ? $this->payment->customer_email : 'NA',
                                'first_name' => $this->payment->firstName(),
                                'last_name' => $this->payment->lastName(),
                            ],
                            "billing_data" => [
                                'email' => filled($this->payment->customer_email) ? $this->payment->customer_email : 'NA',
                                'first_name' => $this->payment->firstName(),
                                'last_name' => $this->payment->lastName(),
                                'phone_number' => filled($this->payment->customer_phone) ? $this->payment->customer_phone : 'NA',
                                'street' => 'NA',
                                'building' => 'NA',
                                'floor' => 'NA',
                                'apartment' => 'NA',
                                'city' => 'NA',
                                'country' => 'NA',
                            ],
                            'extras' => $this->payment->custom_data
                        ]);

        if ($response->json('client_secret') === null) {
            dd($response->body());
            throw (new PaymentGatewayException($response->json('message', 'Cant get payment token')))
                ->setResponse($response->body());
        }

        return $response->json();
    }
}
