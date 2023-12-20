<?php

namespace Hsmfawaz\PaymentGateways;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Facades\PaymentGatewaysFacade;
use Hsmfawaz\PaymentGateways\Gateways\AmazonPay\AmazonGateway;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryGateway;
use Hsmfawaz\PaymentGateways\Gateways\Stripe\StripeGateway;
use Hsmfawaz\PaymentGateways\Gateways\Tamara\TamaraGateway;
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

    public function stripe(): StripeGateway
    {
        return new StripeGateway();
    }

    public function tamara(): TamaraGateway
    {
        return new TamaraGateway();
    }

    public function gateway(string $paymentMethod = ''): Gateway
    {
        return match ($paymentMethod) {
            GatewaysEnum::AMAZON, 'amazon-installment' => PaymentGatewaysFacade::amazon(),
            GatewaysEnum::STRIPE => PaymentGatewaysFacade::stripe(),
            GatewaysEnum::TAMARA => PaymentGatewaysFacade::tamara(),
            default => PaymentGatewaysFacade::fawry(),
        };
    }

    public function getRef(string|Model $identifier): string
    {
        return (config('payment-gateways.ref_generator'))::generate($identifier);
    }
}
