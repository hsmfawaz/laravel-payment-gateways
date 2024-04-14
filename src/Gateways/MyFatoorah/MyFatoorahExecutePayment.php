<?php

namespace Hsmfawaz\PaymentGateways\Gateways\MyFatoorah;

use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;

class MyFatoorahExecutePayment implements NewPayment
{
    public string $invoiceID;

    public function __construct(public PendingPayment $payment)
    {
    }

    public function toResponse(): string|array
    {
        $response = MyFatoorahConfig::get()->request()->post('v2/ExecutePayment', [
            "CustomerName"      => $this->payment->customer_name,
            "CustomerEmail"     => $this->payment->customer_email,
            "InvoiceValue"      => $this->payment->totalAmount(),
            "Language"          => $this->payment->preferred_language,
            "CallBackUrl"       => $this->payment->return_url,
            "ErrorUrl"          => $this->payment->return_url,
            'CustomerReference' => $this->payment->ref,
            "UserDefinedField"  => json_encode($this->payment->custom_data),
            "SessionId"         => $this->payment->ref,
//     "RecurringModel": {
//         "RecurringType": "string",
//         "IntervalDays": 0,
//         "Iteration": 0,
//         "RetryCount": 0
//     },
        ]);
        if ($response->failed() || ! $response->json('IsSuccess')) {
            throw new PaymentGatewayException(
                $response->json('error', 'Cant execute the payment')
            );
        }
        $this->invoiceID = $response->json('Data.InvoiceId');

        return $response->json('Data.PaymentURL');
    }

    public function getRef(): string
    {
        return $this->invoiceID;
    }
}