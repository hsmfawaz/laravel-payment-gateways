<?php

namespace Hsmfawaz\PaymentGateways\Gateways\AmazonPay;

use Hsmfawaz\PaymentGateways\Enum\PaymentCurrency;
use Hsmfawaz\PaymentGateways\PendingPayment;

class AmazonNewPayment
{
    public function __construct(protected PendingPayment $payment)
    {
    }

    public function toResponse()
    {
        return $this->toForm();
    }

    public function toForm(): string
    {
        $properties = "";
        $redirectUrl = AmazonConfig::get()->base_url."/paymentPage";

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
            'merchant_reference' => $this->payment->ref,
            'amount' => $amount,
            'currency' => $this->payment->currency,
            'return_url' => $this->payment->return_url,
            'customer_name ' => $this->payment->customer_name,
            'customer_email' => $this->payment->customer_email,
            'order_description' => $this->payment->description,
        ];
        if ($this->payment->installment) {
            $data['installments'] = 'STANDALONE';
        }
        $data['signature'] = $this->signature($data);

        return $data;
    }

    private function signature(array $data)
    {
        $shaString = '';
        ksort($data);
        foreach ($data as $key => $value) {
            $shaString .= "$key=$value";
        }

        $requestPhrase = AmazonConfig::get()->request_phrase;
        $shaString = $requestPhrase.$shaString.$requestPhrase;

        return hash("SHA256", $shaString);
    }
}