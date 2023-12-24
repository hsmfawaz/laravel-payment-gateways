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

    public string $order_ref;

    public string $payment_ref;

    public string $currency;

    public float $amount;

    public string $transaction_status;

    public string $status;

    public PaymentCustomer $customer;

    public static function fromRequest(array $data): self
    {
        $obj = new self();

        $obj->ref = $data['id'];
        $obj->payment_ref = data_get($data, 'payment.id');
        $obj->order_ref = data_get($data, 'payment.order.reference_id');
        $obj->amount = (float) data_get($data, 'payment.amount');
        $obj->currency = data_get($data, 'payment.currency');
        $obj->transaction_status = strtolower(data_get($data, 'payment.status'));

        $obj->customer = new PaymentCustomer(
            data_get($data, 'payment.buyer.name', ''),
            data_get($data, 'payment.buyer.phone', ''),
            data_get($data, 'payment.buyer.email', ''),
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
        $payment = GatewayPayment::where('ref', $this->payment_ref)->first();
        if ($payment !== null || $this->pending()) {
            return $payment;
        }

        return GatewayPayment::create([
            'ref' => $this->payment_ref,
            'gateway_ref' => $this->ref,
            'model_id' => $model->getKey(),
            'model_type' => $model->getMorphClass(),
            'paid_amount' => $this->amount,
            'currency' => $this->currency,
            'gateway_response' => [
                'order_ref' => $this->order_ref,
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