<?php

namespace Hsmfawaz\PaymentGateways\Enum;

class OrderStatus
{
    public const PAID = 'paid';
    public const PENDING = 'pending';
    public const FAILED = 'failed';
    public const REFUNDED = 'refunded';
}