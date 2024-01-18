<?php

namespace Hsmfawaz\PaymentGateways\Exceptions;

class PaymentGatewayException extends \Exception
{
    protected string $response;

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(string $response)
    {
        $this->response = $response;

        return $this;
    }
}