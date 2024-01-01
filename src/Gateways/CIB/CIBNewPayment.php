<?php

namespace Hsmfawaz\PaymentGateways\Gateways\CIB;

use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class CIBNewPayment implements NewPayment
{
    public string $ref;

    public function __construct(protected PendingPayment $payment)
    {
    }

    public function toResponse(): string|array
    {
        $session = $this->createSession();
        $this->ref = $session['successIndicator'];

        return $session['session']['id'];
    }

    public function createSession()
    {
        $code = CIBConfig::get()->merchant_code;
        $response = $this->request()->post("/merchant/{$code}/session", $this->paymentData());
        if (! $response->created() || $response->json('result') !== 'SUCCESS') {
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
        return [
            'apiOperation' => 'CREATE_CHECKOUT_SESSION',
            'interaction' => [
                'operation' => 'PURCHASE',
                'returnUrl' => $this->payment->return_url,
            ],
            'order' => [
                'id' => $this->payment->ref,
                'amount' => $this->payment->totalAmount(),
                'currency' => $this->payment->currency,
                'description' => $this->payment->description,
            ],
        ];
    }
}