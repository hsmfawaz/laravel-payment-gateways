<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Enum\PaymentStatus;
use Hsmfawaz\PaymentGateways\Models\GatewayPayment;
use Illuminate\Database\Eloquent\Model;

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

        return $obj;
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

    public function failed()
    {
        return ! $this->paid() && ! $this->pending() && ! $this->refunded();
    }

    public function attachTo(Model $model): ?GatewayPayment
    {
        $payment = GatewayPayment::where('ref', $this->ref)->first();
        if ($payment !== null || $this->pending()) {
            return $payment;
        }

        return GatewayPayment::create([
            'ref' => $this->ref,
            'gateway_ref' => $this->fort_id,
            'model_id' => $model->getKey(),
            'model_type' => $model->getMorphClass(),
            //paid_amount has to be dived by the currency cent multiplier
            'paid_amount' => $this->amount,
            'currency' => AmazonConfig::get()->default_currency,
            'gateway_response' => [
                'transaction_status' => $this->transaction_status,
            ],
            'gateway' => GatewaysEnum::AMAZON,
            'status' => $this->status,
        ]);
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}