<?php

namespace EcommerceLayer\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use EcommerceLayer\DomainModels\PaymentData;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class PaymentDataCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return Plan
     */
    public function get($model, $key, $value, $attributes)
    {
        $encodedValue = Arr::get($attributes, $key);
        $value = json_decode($encodedValue, true);

        if (is_null($value)) {
            return null;
        }

        $gatewayKey = $value['gateway_id'];
        unset($value['gateway_id']);

        return new PaymentData($gatewayKey, $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param PaymentData $value
     * @param array $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        if ($value === null) {
            return null;
        }
        
        if (!$value instanceof PaymentData) {
            throw new InvalidArgumentException('The given value is not an PaymentData instance.');
        }

        $value = [
            'gateway_id' => $value->gateway_id,
            ...$value->attributes(),
        ];

        return json_encode($value);
    }
}
