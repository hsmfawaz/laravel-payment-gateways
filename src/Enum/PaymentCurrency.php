<?php

namespace Hsmfawaz\PaymentGateways\Enum;

class PaymentCurrency
{
    public const EGP = 'EGP';

    public const KWD = 'KWD';

    public const USD = 'USD';

    public const EURO = 'EURO';

    public const CRYPTO = 'CRYPTO';

    public static function centsMultiplier(string $currency)
    {
        return match (strtoupper($currency)) {
            self::KWD => 1000,
            default => 100,
        };
    }
}