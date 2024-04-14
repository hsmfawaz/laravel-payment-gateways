<?php

namespace Hsmfawaz\PaymentGateways\Gateways\MyFatoorah;

use Hsmfawaz\PaymentGateways\Contracts\NewPayment;
use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Exceptions\PaymentGatewayException;

class MyFatoorahExecutePayment implements NewPayment
{
    public string $invoiceID;
    public string $recurringID;

    public function __construct(public PendingPayment $payment)
    {
    }

    public function toResponse(): string|array
    {
        $data = [
            "CustomerName"      => $this->payment->customer_name,
            "CustomerEmail"     => $this->payment->customer_email,
            "InvoiceValue"      => $this->payment->totalAmount(),
            "Language"          => $this->payment->preferred_language,
            "CallBackUrl"       => $this->payment->return_url,
            "ErrorUrl"          => $this->payment->return_url,
            'CustomerReference' => $this->payment->ref,
            "UserDefinedField"  => json_encode($this->payment->custom_data),
            "SessionId"         => $this->payment->ref,
        ];

        if ($this->payment->recurring) {
            $data['RecurringModel'] = array_filter([
                "RecurringType" => $this->payment->recurring->type,
                "IntervalDays"  => $this->payment->recurring->interval,
                "Iteration"     => $this->payment->recurring->iteration,
                "RetryCount"    => $this->payment->recurring->retry_count,
            ], fn ($i) => $i !== null);
        }
        $response = MyFatoorahConfig::get()->request()->post('v2/ExecutePayment', $data);
        if ($response->failed() || ! $response->json('IsSuccess')) {
            throw new PaymentGatewayException(
                $response->json('ValidationErrors.0.Error', 'Cant execute the payment')
            );
        }
        $this->invoiceID = $response->json('Data.InvoiceId');
        $this->recurringID = $response->json('Data.RecurringId');

        return $response->json('Data.PaymentURL');
    }

    public function getRef(): string
    {
        return $this->invoiceID;
    }
}