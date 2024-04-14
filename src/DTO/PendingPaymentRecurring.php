<?php

namespace Hsmfawaz\PaymentGateways\DTO;

class PendingPaymentRecurring
{
    public function __construct(
        public string $type = 'Monthly',
        public ?int $interval = null,
        public ?int $iteration = null,
        public int $retry_count = 2,
    ) {
    }

    public static function monthly(): self
    {
        return new self('Monthly');
    }

    public static function custom(int $interval): self
    {
        return new self('Custom', $interval);
    }
}