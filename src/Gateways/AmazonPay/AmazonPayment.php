<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\Enum\PaymentStatus;

class AmazonPayment
{
    public string $ref;

    public string $fort_id;

    public float $amount;

    public string $transaction_status;

    public string $status;

    public static function fromRequest(array $data): self
    {
        $obj = new self();

        $obj->ref = $data['merchant_reference'];
        $obj->fort_id = $data['fort_id'];
        $obj->amount = $data['captured_amount'];
        $obj->transaction_status = $data['transaction_status'];
        $obj->status = match (true) {
            $obj->paid() => PaymentStatus::PAID,
            $obj->pending() => PaymentStatus::PENDING,
            $obj->refunded() => PaymentStatus::REFUNDED,
            default => PaymentStatus::FAILED,
        };

        return new static();
    }

    private function paid()
    {
        return (int) $this->transaction_status === 14;
    }

    private function pending()
    {
        return (int) $this->transaction_status === 19;
    }

    private function refunded()
    {
        return (int) $this->transaction_status === 6;
    }
}