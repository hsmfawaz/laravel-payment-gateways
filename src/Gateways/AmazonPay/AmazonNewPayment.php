<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\DTO\PendingPayment;
use Hsmfawaz\PaymentGateways\Enum\PaymentCurrency;

class AmazonNewPayment
{
    public function __construct(protected PendingPayment $payment)
    {
    }

    public function toForm(): string
    {
        $properties = "";
        $redirectUrl = $this->baseUrl();

        foreach ($this->paymentData() as $key => $property) {
            $properties .= "<input type='hidden' name='$key' value='$property' />\n";
        }

        return <<<HTML
           <form action='$redirectUrl' method='post' name='frm'>
                $properties
                 <script type='text/javascript'> document.frm.submit() </script>
            </form>
       HTML;
    }

    public function paymentData()
    {
        $amount = $this->payment->totalAmount() * PaymentCurrency::centsMultiplier($this->payment->currency);
        $data = [
            'access_code' => AmazonConfig::get()->security_key,
            'merchant_identifier' => AmazonConfig::get()->merchant_code,
            'language' => $this->payment->preferred_language,
            'command' => 'PURCHASE',
            'merchant_reference' => str_replace('|', '-', $this->payment->ref),
            'amount' => $amount,
            'currency' => $this->payment->currency,
            'return_url' => $this->payment->return_url,
            'customer_email' => $this->payment->customer_email,
            'order_description' => $this->payment->description,
        ];
        if ($this->payment->installment) {
            $data['installments'] = 'STANDALONE';
        }
        $data['signature'] = AmazonSignature::get($data);

        return $data;
    }

    public function toResponse()
    {
        return $this->toForm();
    }

    private function baseUrl(): string
    {
        return AmazonConfig::get()->live ?
            'https://checkout.payfort.com/FortAPI/paymentPage' :
            'https://sbcheckout.payfort.com/FortAPI/paymentPage';
    }
}