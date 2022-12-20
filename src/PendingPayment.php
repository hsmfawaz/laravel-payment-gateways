<?php

namespace Hsmfawaz\PaymentGateways;

class PendingPayment
{
    public function __construct(
        public string $ref,
        public string $preferred_language,
        public string $customer_email,
        public string $customer_phone,
        public string $customer_name,
        public string $return_url,
        public string $currency,
        public string $description,
        public int $expire_after,
        public array $items,
    ) {
    }
}