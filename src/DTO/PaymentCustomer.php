<?php

namespace Hsmfawaz\PaymentGateways\DTO;

use Illuminate\Contracts\Support\Arrayable;

class PaymentCustomer implements Arrayable
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
    ) {
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}