<?php

namespace Hsmfawaz\PaymentGateways\Models;

use Illuminate\Database\Eloquent\Model;

class GatewayPayment extends Model
{
    protected $guarded = [];
    protected $casts = ['gateway_response' => 'json'];
}