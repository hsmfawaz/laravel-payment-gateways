<?php

namespace Hsmfawaz\PaymentGateways;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Facades\PaymentGatewaysFacade;
use Hsmfawaz\PaymentGateways\Gateways\AmazonPay\AmazonGateway;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryGateway;
use Illuminate\Database\Eloquent\Model;

class PaymentGateways
{
    public function fawry(): FawryGateway
    {
        return new FawryGateway();
    }

    public function amazon(): AmazonGateway
    {
        return new AmazonGateway();
    }

    public function gateway(string $paymentMethod = ''): Gateway
    {
        return match ($paymentMethod) {
            'amazon', 'amazon-installment' => PaymentGatewaysFacade::amazon(),
            default => PaymentGatewaysFacade::fawry(),
        };
    }

    public function getRef(string|Model $identifier): string
    {
        return (config('payment-gateways.ref_generator'))::generate($identifier);
    }
}
