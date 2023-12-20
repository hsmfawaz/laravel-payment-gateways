<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Tamara;

use Illuminate\Contracts\Support\Arrayable;

class TamaraPayment implements Arrayable
{
    public function toArray()
    {
        return get_object_vars($this);
    }
}