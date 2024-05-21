<?php

namespace Hsmfawaz\PaymentGateways\Gateways\MyFatoorah;

use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;

class MyFatoorahInitPayment
{
    /**
     * @var MyFatoorahPaymentMethod[]
     */
    public array $methods = [];


    public function __construct(public float $amount, public ?string $currency = null)
    {
        $this->currency = $currency ?? MyFatoorahConfig::get()->default_currency;
        $this->get();
    }

    private function get(): void
    {
        $response = MyFatoorahConfig::get()->request()->post('v2/InitiatePayment', [
            "InvoiceAmount" => $this->amount,
            "CurrencyIso"   => $this->currency,
        ]);

        if ($response->failed() || ! $response->json('IsSuccess')) {
            throw new PaymentGatewayException(
                $response->json('Message', 'Cant initiate a new payment')
            );
        }

        $this->methods = $response->json('Data.PaymentMethods');
    }

    /**
     * @param  bool  $status
     *
     * @return MyFatoorahPaymentMethod[]
     */
    public function embedded(bool $status = true): array
    {
        return array_filter($this->methods, fn ($method) => $method['IsEmbeddedSupported'] === $status);
    }
}