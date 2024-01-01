<?php

namespace Hsmfawaz\PaymentGateways\Gateways\CIB;

use Hsmfawaz\PaymentGateways\DTO\PaymentCustomer;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Enum\PaymentStatus;
use Hsmfawaz\PaymentGateways\Models\GatewayPayment;
use Illuminate\Database\Eloquent\Model;

class CIBPayment
{
    public string $ref;

    public float $amount;

    public PaymentCustomer $customer;

    public string $currency;

    public string $result;

    public string $status;

    public static function fromRequest(array $data)
    {
        $obj = new static();
        $obj->ref = $data['id'];
        $obj->currency = $data['currency'];
        $obj->amount = $data['amount'];
        $obj->result = $data['result'];
        $obj->customer = new PaymentCustomer(
            implode(" ", data_get($data, 'customer', [])),
            "",
            ""
        );

        $obj->status = match (true) {
            $obj->paid() => PaymentStatus::PAID,
            $obj->pending() => PaymentStatus::PENDING,
            $obj->refunded() => PaymentStatus::REFUNDED,
            default => PaymentStatus::FAILED,
        };

        return $obj;
    }

    public function paid()
    {
        return $this->result === "SUCCESS";
    }

    public function pending()
    {
        return $this->result === "PENDING";
    }

    public function refunded()
    {
        return false;
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
            'gateway_ref' => $this->ref,
            'model_id' => $model->getKey(),
            'model_type' => $model->getMorphClass(),
            //paid_amount has to be dived by the currency cent multiplier
            'paid_amount' => $this->amount,
            'currency' => $this->currency,
            'gateway_response' => [
                'transaction_status' => $this->result,
            ],
            'gateway' => GatewaysEnum::CIB,
            'status' => $this->status,
        ]);
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}