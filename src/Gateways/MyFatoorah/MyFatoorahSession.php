<?php

namespace Hsmfawaz\PaymentGateways\Gateways\MyFatoorah;

use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;
use JetBrains\PhpStorm\ArrayShape;

class MyFatoorahSession
{
    public function __construct(public string $id)
    {
    }

    #[ArrayShape(['cards' => "array", 'id' => "string", 'country_code' => "string"])]
    public function get(): array
    {
        $response = MyFatoorahConfig::get()->request()->post('v2/InitiateSession', [
            "CustomerIdentifier" => $this->id,
            "SaveToken"          => true,
        ]);

        if ($response->failed() || ! $response->json('IsSuccess')) {
            throw new PaymentGatewayException(
                $response->json('error', 'Cant initiate a new session')
            );
        }

        return [
            'cards'        => $response->json('Data.CustomerTokens'),
            'id'           => $response->json('Data.SessionId'),
            'country_code' => $response->json('Data.CountryCode'),
        ];
    }

    public function update(string $sessionID, string $token, string $cvv): string
    {
        $response = MyFatoorahConfig::get()->request()->post('v2/UpdateSession', [
            "SessionId"    => $sessionID,
            "Token"        => $token,
            'TokenType'    => 'mftoken',
            "SecurityCode" => $cvv,
        ]);

        if ($response->failed() || ! $response->json('IsSuccess')) {
            throw new PaymentGatewayException(
                $response->json('error', 'Cant update the session')
            );
        }

        return $response->json('Data.SessionId');
    }

    public function delete(string $token)
    {
        $response = MyFatoorahConfig::get()->request()->post('v2/CancelToken?Token='.$token);
        if ($response->failed() || ! $response->json('IsSuccess')) {
            return false;
        }

        return true;
    }
}