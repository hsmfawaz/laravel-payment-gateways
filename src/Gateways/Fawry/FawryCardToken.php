<?php

namespace Hsmfawaz\PaymentGateways\Gateways\Fawry;

use Hsmfawaz\PaymentGateways\Models\GatewayToken;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FawryCardToken implements Arrayable
{
    public string $brand;
    public string $last_four_digits;
    public string $token;
    public int $creation_date;

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

    public function attachTo(Model $model): ?GatewayToken
    {
        $token = GatewayToken::where('token', $this->token)->first();
        if ($token !== null) {
            return $token;
        }

        return GatewayToken::create([
            'brand'            => $this->brand,
            'last_four_digits' => $this->last_four_digits,
            'token'            => $this->token,
            'creation_date'    => $this->creation_date,
            'model_id'         => $model->getKey(),
            'model_type'       => $model->getMorphClass(),
        ]);
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

}