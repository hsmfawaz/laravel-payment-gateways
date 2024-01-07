<?php

namespace Hsmfawaz\PaymentGateways\Gateways\CIB;

use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class CIBNewPayment implements NewPayment
{
    public function __construct(protected PendingPayment $payment)
    {
    }

    public function toResponse(): string|array
    {
        $session = $this->createSession();

        return $session['session']['id'];
    }

    public function createSession()
    {
        $code = CIBConfig::get()->merchant_code;
        $response = $this->request()->post("/merchant/{$code}/session", $this->paymentData());
        if (! $response->created() || $response->json('result') !== 'SUCCESS') {
            throw new PaymentGatewayException(
                $response->json('error.explanation', 'Cant initiate a new payment')
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
        $config = CIBConfig::get();
        $auth = base64_encode("merchant.{$config->merchant_code}:{$config->security_key}");

        return Http::asJson()
            ->asJson()
            ->acceptJson()
            ->withToken($auth, 'Basic')
            ->baseUrl($config->base_url);
    }

    private function paymentData()
    {
        $config = CIBConfig::get();

        return [
            'apiOperation' => 'INITIATE_CHECKOUT',
            'customer' => [
                'email' => $this->payment->customer_email,
                'firstName' => $this->payment->firstName(),
                'lastName' => $this->payment->lastName(),
                'mobilePhone' => $this->payment->customer_phone,
            ],
            'interaction' => [
                'operation' => 'PURCHASE',
                'locale' => $this->payment->preferred_language === 'ar' ? "ar_EG" : "en_US",
                'returnUrl' => $this->payment->return_url."?ref=".$this->payment->ref."&merchant_code=".$config->merchant_code,
                'cancelUrl' => $this->payment->return_url,
                'merchant' => [
                    'name' => $config->merchant_name,
                    'logo' => $config->merchant_logo,
                    'url' => $config->merchant_website,
                ],
            ],
            'order' => [
                'id' => $this->payment->ref,
                'amount' => $this->payment->totalAmount(),
                'currency' => $this->payment->currency,
                'description' => $this->payment->description,
                'notificationUrl' => $this->payment->return_url."?ref=".$this->payment->ref,
            ],
        ];
    }
}