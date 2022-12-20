<?php

namespace Hsmfawaz\PaymentGateways;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentRefGenerator
{
    public static function generate(string|Model $identifier, string $type = 'random'): string
    {
        if ($identifier instanceof Model) {
            $type = self::modelToType($identifier);
            $identifier = $identifier->getKey();
        }

        return $identifier."|".$type."|".microtime(true);
    }

    public static function modelToType(Model $identifier): string
    {
        $split = explode('\\', Str::of($identifier->getMorphClass()));

        return (string) str()->snake(end($split));
    }
}