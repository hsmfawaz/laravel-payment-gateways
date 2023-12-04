<?php

namespace Hsmfawaz\PaymentGateways\DTO;

use Hsmfawaz\PaymentGateways\Gateways\AmazonPay\AmazonPayment;
use Hsmfawaz\PaymentGateways\Gateways\Fawry\FawryPayment;
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
        public AmazonPayment|FawryPayment $payment,
    ) {
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}