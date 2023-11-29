<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Fawry;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentNotFoundException;
use Hsmfawaz\PaymentGateways\PendingPayment;

class FawryGateway extends FawrySetup implements Gateway
{
    public function captureCardToken(string $ref, string $returnUrl)
    {
        $baseUrl = $this->live
            ? 'https://www.atfawry.com/atfawry/plugin/card-token'
            : 'https://atfawry.fawrystaging.com/atfawry/plugin/card-token';

        return $baseUrl."?accNo=".$this->merchant_code."&customerProfileId=".$ref."&returnUrl=".$returnUrl;
    }

    /**
     * @return FawryCardToken[]
     * @throws PaymentGatewayException
     */
    public function tokensList(string $ref): array
    {
        $response = $this->request()->get('cards/cardToken', $this->queryParams([
                'merchantCode'      => $this->merchant_code,
                'customerProfileId' => $ref,
                'signature'         => $this->signature($ref),
            ]
        ));
        if (! $response->ok() || (int) $response->json('statusCode') !== 200) {
            throw new PaymentGatewayException($response->json('description', 'Cant fetch ref  cards: '.$ref));
        }

        return array_map(fn ($i) => FawryCardToken::fromRequest($i), $response->json('cards') ?? []);
    }

    public function deleteToken(string $ref, string $token): bool
    {
        $response = $this->request()->delete('cards/cardToken?'.$this->queryParams([
                    'merchantCode'      => $this->merchant_code,
                    'cardToken'         => $token,
                    'customerProfileId' => $ref,
                    'signature'         => $this->signature($ref.$token),
                ]
            ));

        return (int) $response->json('statusCode') === 200;
    }

    public function create(PendingPayment $payment)
    {
        return new FawryNewPayment($payment);
    }

    /**
     * @throws PaymentNotFoundException
     */
    public function get(string $ref): FawryPayment
    {
        $response = $this->request()->get('payments/status/v2', $this->queryParams([
                'merchantCode'      => $this->merchant_code,
                'merchantRefNumber' => $ref,
                'signature'         => $this->signature($ref),
            ]
        ));
        if (! $response->ok() || $response->json('code') !== null) {
            throw new PaymentNotFoundException($response->json('description', 'Cant fetch payment ref : '.$ref));
        }

        return FawryPayment::fromRequest($response->json());
    }
}