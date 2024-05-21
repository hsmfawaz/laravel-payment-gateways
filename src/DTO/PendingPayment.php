<?php

namespace Hsmfawaz\PaymentGateways\DTO;

use Illuminate\Contracts\Support\Arrayable;

class PendingPayment implements Arrayable
{
    public function __construct(
        public string $ref,
        public string $preferred_language,
        public string $customer_email,
        public string $customer_phone,
        public string $customer_name,
        public string $currency,
        public string $description,
        public array $items,
        public int $expire_after = 0,
        public string $return_url = '',
        public string $method = 'gateway',
        public string $cardToken = '',
        public string $cardCvv = '',
        public bool|int $installment = false,
        public array $custom_data = [],
        public ?PendingPaymentRecurring $recurring = null,
    ) {
    }

    public function firstName()
    {
        return explode(' ', $this->customer_name)[0];
    }

    public function lastName()
    {
        return last(explode(' ', $this->customer_name));
    }

    public function totalAmount(): float
    {
        return (float) number_format(collect($this->items)->sum(function ($i) {
            return $i['quantity'] * $i['price'];
        }), 2, '.', '');
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
