<?php

namespace Hsmfawaz\PaymentGateways;

use Hsmfawaz\PaymentGateways\Contracts\Gateway;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Gateways\AmazonPay\AmazonGateway;
use Hsmfawaz\PaymentGateways\Gateways\CIB\CIBGateway;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryGateway;
use Hsmfawaz\PaymentGateways\Gateways\Paymob\PaymobGateway;
use Hsmfawaz\PaymentGateways\Gateways\Stripe\StripeGateway;
use Hsmfawaz\PaymentGateways\Gateways\Tabby\TabbyGateway;
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

    public function tabby(): TabbyGateway
    {
        return new TabbyGateway();
    }

    public function cib(): CIBGateway
    {
        return new CIBGateway();
    }

    public function paymob(): PaymobGateway
    {
        return new PaymobGateway();
    }

    public function gateway(string $paymentMethod = ''): Gateway
    {
        return match ($paymentMethod) {
            GatewaysEnum::AMAZON, 'amazon-installment' => $this->amazon(),
            GatewaysEnum::STRIPE => $this->stripe(),
            GatewaysEnum::TAMARA => $this->tamara(),
            GatewaysEnum::TABBY => $this->tabby(),
            GatewaysEnum::CIB => $this->cib(),
            GatewaysEnum::PAYMOB => $this->paymob(),
            default => $this->fawry(),
        };
    }

    public function getRef(string|Model $identifier): string
    {
        return (config('payment-gateways.ref_generator'))::generate($identifier);
    }
}
