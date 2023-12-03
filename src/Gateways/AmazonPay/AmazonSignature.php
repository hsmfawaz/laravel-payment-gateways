<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

class AmazonSignature
{
    public static function get($data): string
    {
        $shaString = '';
        ksort($data);
        foreach ($data as $key => $value) {
            $shaString .= "$key=$value";
        }

        $requestPhrase = AmazonConfig::get()->request_phrase;
        $shaString = $requestPhrase.$shaString.$requestPhrase;

        return hash("SHA256", $shaString);
    }
}