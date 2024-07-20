<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Paymob;

use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Enum\PaymentCurrency;
use Hsmfawaz\PaymentGateways\Enum\PaymentStatus;
use Hsmfawaz\PaymentGateways\Models\GatewayPayment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymobPayment implements Arrayable
{
    public string $id;
    public bool $pending;
    public bool $success;
    public bool $is_refunded;
    public array $order;

    public string $currency;

    public string $status;
    private string $type = GatewaysEnum::PAYMOB;

    public static function fromRequest(array $response): self
    {
        $obj = new static();
        foreach ($response as $key => $value) {
            $snakeKey = Str::snake($key);
            if (property_exists($obj, $snakeKey)) {
                $obj->$snakeKey = $value;
            }
        }

        $obj->status = match (true) {
            $obj->paid() => PaymentStatus::PAID,
            $obj->pending() => PaymentStatus::PENDING,
            $obj->refunded() => PaymentStatus::REFUNDED,
            default => PaymentStatus::FAILED,
        };

        return $obj;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function paid(): bool
    {
        return $this->success;
    }

    public function pending(): bool
    {
        return $this->pending;
    }

    public function refunded(): bool
    {
        return $this->is_refunded;
    }

    public function failed(): bool
    {
        return ! $this->paid() && ! $this->pending() && ! $this->refunded();
    }

    public function attachTo(Model $model): ?GatewayPayment
    {
        $payment = GatewayPayment::where('ref', $this->order['merchant_order_id'])->first();
        if ($payment !== null || $this->pending()) {
            return $payment;
        }

        return GatewayPayment::create([
            'ref' => $this->order['merchant_order_id'],
            'gateway_ref' => $this->id ?? 'unknown',
            'model_id' => $model->getKey(),
            'model_type' => $model->getMorphClass(),
            'paid_amount' => $this->order['paid_amount_cents'] / 100,
            'currency' => $this->currency ?? PaymentCurrency::EGP,
            'gateway_response' => [
                'request_id' => $this->id,
                'order' => $this->order,
                'status' => $this->status,
            ],
            'gateway' => $this->type,
            'status' => $this->paid() ? PaymentStatus::PAID : PaymentStatus::FAILED,
        ]);
    }


}
