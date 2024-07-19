<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Tabby;

use Hsmfawaz\PaymentGateways\DTO\PaymentCustomer;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Enum\PaymentStatus;
use Hsmfawaz\PaymentGateways\Models\GatewayPayment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class TabbyPayment implements Arrayable
{
    public string $ref;

    public string $tabby_ref;

    public string $currency;

    public float $amount;

    public string $transaction_status;

    public string $status;

    public PaymentCustomer $customer;

    public static function fromRequest(array $data): self
    {
        $obj = new self();
        $obj->ref = data_get($data, 'order.reference_id');
        $obj->tabby_ref = data_get($data, 'id');
        $obj->amount = (float) data_get($data, 'amount');
        $obj->currency = data_get($data, 'currency');
        $obj->transaction_status = strtolower(data_get($data, 'status'));

        $obj->customer = new PaymentCustomer(
            data_get($data, 'buyer.name', ''),
            data_get($data, 'buyer.phone', ''),
            data_get($data, 'buyer.email', ''),
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
        return $this->transaction_status === 'authorized';
    }

    public function pending()
    {
        return $this->transaction_status === 'created';
    }

    public function refunded()
    {
        return $this->transaction_status === 'closed';
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
            'gateway_ref' => $this->tabby_ref,
            'model_id' => $model->getKey(),
            'model_type' => $model->getMorphClass(),
            'paid_amount' => $this->amount,
            'currency' => $this->currency,
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