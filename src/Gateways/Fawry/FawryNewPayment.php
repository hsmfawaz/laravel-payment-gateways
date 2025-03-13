<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Fawry;

use Carbon\Carbon;
use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Http;

class FawryNewPayment implements NewPayment
{
    public function __construct(protected PendingPayment $payment)
    {
    }

    public function toResponse(): string|array
    {
        return $this->paymentUrl();
    }

    public function paymentUrl(): string
    {
        $response = Http::timeout(30)->post($this->baseUrl(), $this->paymentData());
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
            'merchantCode' => FawryConfig::get()->merchant_code,
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
        $config = FawryConfig::get();

        return hash('sha256', $config->merchant_code.$content.$config->security_key);
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
        return FawryConfig::get()->live
            ? 'https://atfawry.com/fawrypay-api/api/payments/init'
            : 'https://atfawry.fawrystaging.com/fawrypay-api/api/payments/init';
    }

    public function getRef(): string
    {
        return $this->payment->ref;
    }
}
