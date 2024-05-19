<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Paymob;

use Hsmfawaz\PaymentGateways\DTO\PaidPayment;
use Hsmfawaz\PaymentGateways\DTO\PaymentCustomer;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentNotFoundException;
use Hsmfawaz\PaymentGateways\Gateways\Paymob\PaymobPayment;
use Illuminate\Support\Facades\Http;

class PaymobGetPayment
{
    public function handle(string $ref)
    {
        $response = $this->request($ref);

        if (!$response->ok()) {
            throw new PaymentNotFoundException(
                $response->json('description', 'Cant fetch payment ref : ' . $ref)
            );
        }


        return $this->toPaidPayment(PaymobPayment::fromRequest($response->json()));

    }

    private function request(string $ref)
    {
        return Http::baseUrl(PaymobConfig::get()->base_url)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->getAuthToken(),
            ])
            ->get('acceptance/transactions/' . $ref);
    }


    private function getAuthToken()
    {
        $response = Http::post(PaymobConfig::get()->base_url . '/auth/tokens', [
            'api_key' => PaymobConfig::get()->api_key,
        ]);
        if (($response->json('token') ?? null) === null) {
            throw new PaymentGatewayException($response->json('message',
                'Cant get auth token'));
        }

        return $this->authToken = $response->json('token');
    }

    private function toPaidPayment(PaymobPayment $payment): PaidPayment
    {
        return new PaidPayment(
            ref: $payment->order['merchant_order_id'],
            gateway: GatewaysEnum::PAYMOB,
            amount: $payment->order['amount_cents'] / 100,
            currency: $payment->currency,
            status: $payment->status,
            customer: new PaymentCustomer(
                name: $payment->order['shipping_data']['first_name'] . ' ' . $payment->order['shipping_data']['last_name'],
                phone: $payment->order['shipping_data']['phone_number'],
                email: $payment->order['shipping_data']['email'],
            ),
            payment: $payment,
        );
    }


}
