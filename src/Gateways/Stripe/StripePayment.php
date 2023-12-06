<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Stripe;

use Hsmfawaz\PaymentGateways\DTO\PaymentCustomer;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Enum\PaymentCurrency;
use Hsmfawaz\PaymentGateways\Enum\PaymentStatus;
use Hsmfawaz\PaymentGateways\Models\GatewayPayment;
use Illuminate\Database\Eloquent\Model;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;

class StripePayment
{
    public string $ref;

    public string $currency;

    public float $amount;

    public string $status;

    public ?PaymentCustomer $customer = null;

    public static function fromIntent(PaymentIntent $payment)
    {
        $obj = new self();
        $obj->ref = $payment->id;
        $obj->currency = strtoupper($payment->currency);
        $obj->amount = $payment->amount_received / PaymentCurrency::centsMultiplier($obj->currency);

        $obj->customer = new PaymentCustomer('', '', '',);

        $obj->status = match ($payment->status) {
            'succeeded' => PaymentStatus::PAID,
            'processing' => PaymentStatus::PENDING,
            default => PaymentStatus::FAILED,
        };

        return $obj;
    }

    public function paid()
    {
        return $this->status === PaymentStatus::PAID;
    }

    public function pending()
    {
        return $this->status === PaymentStatus::PENDING;
    }

    public function refunded()
    {
        return false;
    }

    public function failed()
    {
        return $this->status === PaymentStatus::FAILED;
    }

    public function toArray()
    {
        return get_object_vars($this);
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
            'paid_amount' => $this->amount,
            'currency' => $this->currency,
            'gateway_response' => [],
            'gateway' => GatewaysEnum::STRIPE,
            'status' => $this->status,
        ]);
    }

    public static function fromSession(Session $session)
    {
        $obj = new self();
        $obj->ref = $session->id;
        $obj->currency = $session->currency;
        $obj->amount = $session->amount_total / PaymentCurrency::centsMultiplier($obj->currency);

        $obj->customer = new PaymentCustomer(
            name: $session->customer_details?->name ?? '',
            phone: $session->customer_details?->phone ?? '',
            email: $session->customer_details?->email ?? '',
        );

        $obj->status = match ($session->payment_status) {
            'paid' => PaymentStatus::PAID,
            'unpaid' => PaymentStatus::PENDING,
            default => PaymentStatus::FAILED,
        };

        return $obj;
    }
}