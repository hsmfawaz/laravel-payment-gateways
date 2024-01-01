<?php

namespace Hsmfawaz\PaymentGateways\DTO;

use Hsmfawaz\PaymentGateways\Gateways\AmazonPay\AmazonPayment;
use Hsmfawaz\PaymentGateways\Gateways\CIB\CIBPayment;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryPayment;
use Hsmfawaz\PaymentGateways\Gateways\Stripe\StripePayment;
use Hsmfawaz\PaymentGateways\Gateways\Tabby\TabbyPayment;
use Hsmfawaz\PaymentGateways\Gateways\Tamara\TamaraPayment;
use Illuminate\Contracts\Support\Arrayable;

class PaidPayment implements Arrayable
{
    public function __construct(
        public string $ref,
        public string $gateway,
        public float $amount,
        public string $currency,
        public string $status,
        public PaymentCustomer $customer,
        public AmazonPayment|FawryPayment|StripePayment|TabbyPayment|TamaraPayment|CIBPayment $payment,
    ) {
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}