<?php

namespace Hsmfawaz\PaymentGateways\Gateways\MyFatoorah;

use Hsmfawaz\PaymentGateways\DTO\PaymentCustomer;
use Hsmfawaz\PaymentGateways\Enum\GatewaysEnum;
use Hsmfawaz\PaymentGateways\Enum\PaymentStatus;
use Hsmfawaz\PaymentGateways\Models\GatewayPayment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class MyFatoorahPayment implements Arrayable
{
    public string $ref;

    public string $currency;

    public float $amount;

    public string $transaction_status;

    public string $status;

    public string $error_message;
    public string $userDefinedField;

    public PaymentCustomer $customer;

    public static function fromRequest(array $data): self
    {
        $obj = new self();
        $obj->ref = data_get($data, 'InvoiceId');
        $obj->amount = (float) data_get($data, 'InvoiceValue');
        $obj->currency = data_get($data, 'InvoiceTransactions.0.Currency');
        $obj->transaction_status = data_get($data, 'InvoiceStatus');
        $obj->error_message = $obj->getErrorMessage(data_get($data, 'InvoiceTransactions.0.ErrorCode'));
        $obj->userDefinedField = data_get($data, 'UserDefinedField');
        $obj->customer = new PaymentCustomer(
            data_get($data, 'CustomerName', ''),
            data_get($data, 'CustomerMobile', ''),
            data_get($data, 'CustomerEmail', ''),
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
        return $this->transaction_status === 'Paid';
    }

    public function pending()
    {
        return $this->transaction_status === 'Pending';
    }

    public function refunded()
    {
        return $this->transaction_status === 'Canceled';
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
            'ref'              => $this->ref,
            'gateway_ref'      => $this->ref,
            'model_id'         => $model->getKey(),
            'model_type'       => $model->getMorphClass(),
            'paid_amount'      => $this->amount,
            'currency'         => $this->currency,
            'gateway_response' => [
                'transaction_status' => $this->transaction_status,
            ],
            'gateway'          => GatewaysEnum::MYFATOORAH,
            'status'           => $this->status,
        ]);
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    private function getErrorMessage($code)
    {
        return match ($code) {
            'MF001' => __("3DS authentication failed, possible reasons (user inserted a wrong password, cardholder/card issuer are not enrolled with 3DS, or the issuer bank has technical issue)."),
            'MF002' => __("The issuer bank has declined the transaction, possible reasons (invalid card details, insufficient funds, denied by risk, the card is expired/held, or card is not enabled for online purchase)."),
            'MF003' => __("The transaction has been blocked from the gateway, possible reasons (unsupported card BIN, fraud detection, or security blocking rules)."),
            'MF004' => __("Insufficient funds"),
            'MF005' => __("Session timeout"),
            'MF006' => __("Transaction canceled"),
            'MF007' => __("The card is expired."),
            'MF008' => __("The card issuer doesn't respond."),
            'MF009' => __("Denied by Risk"),
            'MF010' => __("Wrong Security Code"),
            default => __('Unspecified Failure'),
        };
    }
}