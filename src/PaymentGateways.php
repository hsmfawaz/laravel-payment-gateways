<?php

namespace Hsmfawaz\PaymentGateways;

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

    public function getRef(string|Model $identifier): string
    {
        return (config('payment-gateways.ref_generator'))::generate($identifier);
    }
}
