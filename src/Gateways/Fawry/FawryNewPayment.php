<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Fawry;

use Carbon\Carbon;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Hsmfawaz\PaymentGateways\PendingPayment;
use Illuminate\Support\Facades\Http;

class FawryNewPayment
{
    private string $merchant_code;

    private string $security_key;

    public function __construct(protected PendingPayment $payment)
    {
        $this->merchant_code = config('payment-gateways.gateways.fawry.merchant_code');
        $this->security_key = config('payment-gateways.gateways.fawry.security_key');
    }

    public function toResponse()
    {
        return $this->paymentUrl();
    }

    public function paymentUrl(): string
    {
        $response = Http::post($this->baseUrl(), $this->paymentData());
        if (! $response->ok() || $response->json('code') !== null) {
            throw new PaymentGatewayException($response->json('description',
                'Cant initiate a new payment'));
        }

        return $response->body();
    }

    public function paymentData()
    {
        $items = $this->transformItems($this->payment->items);
        $signatureContent = $this->payment->ref.$this->payment->return_url.$this->itemsSignature($items);

        return [
            'merchantCode' => $this->merchant_code,
            'merchantRefNum' => $this->payment->ref,
            'paymentExpiry' => Carbon::now()
                    ->addMinutes($this->payment->expire_after)->timestamp * 1000,
            'locale' => $this->payment->preferred_language,
            'customerMobile' => $this->payment->customer_phone,
            'customerEmail' => $this->payment->customer_email,
            'customerName' => $this->payment->customer_name,
            'returnUrl' => $this->payment->return_url,
            'authCaptureModePayment' => false,
            'currencyCode' => $this->payment->currency,
            'chargeItems' => $items,
            'description' => $this->payment->description,
            'signature' => $this->signature($signatureContent),
        ];
    }

    private function signature(string $content)
    {
        return hash('sha256', $this->merchant_code.$content.$this->security_key);
    }

    private function transformItems(array $items)
    {
        return array_map(static fn ($i) => [
            'itemId' => $i['id'],
            'quantity' => $i['quantity'],
            'price' => number_format($i['price'], 2, '.', ''),
        ], $items);
    }

    private function itemsSignature(array $items)
    {
        $signature = "";
        foreach ($items as $item) {
            $signature .= $item['itemId'].$item['quantity'].$item['price'];
        }

        return $signature;
    }

    private function baseUrl()
    {
        return config('payment-gateways.gateways.fawry.live', false)
            ? 'https://atfawry.com/fawrypay-api/api/payments/init'
            : 'https://atfawry.fawrystaging.com/fawrypay-api/api/payments/init';
    }

    public function charge(): array
    {
        $endpoint = config('payment-gateways.gateways.fawry.live', false)
            ? "https://www.atfawry.com/ECommerceWeb/Fawry/payments/charge"
            : "https://atfawry.fawrystaging.com/ECommerceWeb/Fawry/payments/charge";

        $data = $this->paymentData() + [
                'amount' => $this->payment->totalAmount(),
                'cvv' => $this->payment->cardCvv,
                'cardToken' => $this->payment->cardToken,
                'signature' => $this->getChargeSignature(),
                'paymentMethod' => $this->payment->method,
            ];
        $response = Http::asJson()->acceptJson()->post($endpoint, $data);
        if (! $response->ok() || $response->json('code') !== null) {
            throw new PaymentGatewayException($response->json('description',
                'Cant charge the selected credit card'));
        }

        return $response->json();
    }

    protected function getChargeSignature(): string
    {
        return $this->signature($this->payment->ref.$this->payment->method.$this->payment->totalAmount().$this->payment->cardToken.$this->payment->cardCvv);
    }
}