<?php

namespace Hsmfawaz\PaymentGateways\Contracts;

interface NewPayment
{
    public function toResponse(): string|array;

    public function getRef(): string;
}