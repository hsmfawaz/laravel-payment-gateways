<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Fawry;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class FawryPayment implements Arrayable
{
    public string $request_id;
    public string $fawry_ref_number;
    public string $merchant_ref_number;
    public string $customer_name;
    public string $customer_mobile;
    public string $customer_mail;
    public float $payment_amount;
    public float $order_amount;
    public float $fawry_fees;
    public string $order_status;
    public string $payment_method;
    public int $payment_time;
    public string $auth_number;
    public string $message_signature;
    public string $payment_refrence_number;
    public int $order_expiry_date;
    public array $order_items;
    public array $three_ds_info;

    public static function fromRequest(array $response): self
    {
        $obj = new static();
        foreach ($response as $key => $value) {
            $snakeKey = Str::snake($key);
            if (property_exists($obj, $snakeKey)) {
                $obj->$snakeKey = $value;
            }
        }

        return $obj;
    }

    public function paid()
    {
        return $this->order_status === 'PAID';
    }

    public function pending()
    {
        return $this->order_status === 'New';
    }

    public function refunded()
    {
        return in_array($this->order_status, ['PARTIAL_REFUNDED', 'REFUNDED']);
    }

    public function failed()
    {
        return in_array($this->order_status, ['FAILED', 'EXPIRED', 'CANCELED']);
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}