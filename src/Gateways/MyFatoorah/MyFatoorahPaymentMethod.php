<?php

namespace Hsmfawaz\PaymentGateways\Gateways\MyFatoorah;

use Illuminate\Contracts\Support\Arrayable;

class MyFatoorahPaymentMethod implements Arrayable
{
    public function __construct(
        public int $PaymentMethodId,
        public string $PaymentMethodAr,
        public string $PaymentMethodEn,
        public string $PaymentMethodCode,
        public bool $IsDirectPayment,
        public float $ServiceCharge,
        public float $TotalAmount,
        public string $CurrencyIso,
        public string $ImageUrl,
        public bool $IsEmbeddedSupported,
        public string $PaymentCurrencyIso,
    ) {
    }

    public function name(string $lang = 'en')
    {
        return $lang === 'ar' ? $this->PaymentMethodAr : $this->PaymentMethodEn;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}