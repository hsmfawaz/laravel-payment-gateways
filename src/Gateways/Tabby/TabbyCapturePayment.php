<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Tabby;

use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Http;

class TabbyCapturePayment
{
    public function handle(string $paymentID, float $amount)
    {
        $response = Http::withToken(TabbyConfig::get()->security_key)
            ->asJson()
            ->acceptJson()
            ->post("https://api.tabby.ai/api/v1/payments/{$paymentID}/captures", [
                'amount' => $amount,
            ]);
        $closed = strtoupper($response->json('status')) === 'CLOSED';
        if (! $response->ok()) {
            throw new PaymentGatewayException(
                $response->json('error', 'Tabby: Cant capture Payment '.$paymentID)
            );
        }

        return $closed;
    }
}