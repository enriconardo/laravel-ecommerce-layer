<?php

namespace EcommerceLayer\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use EcommerceLayer\Models\PaymentMethod;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class PaymentMethodCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return PaymentMethod
     */
    public function get($model, $key, $value, $attributes)
    {
        $encodedValue = Arr::get($attributes, $key);
        $value = json_decode($encodedValue, true);
        return is_null($value) 
            ? null 
            : new PaymentMethod(
                $value['type'], 
                Arr::get($value, 'data', []), 
                Arr::get($value, 'gateway_key')
            );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param PaymentMethod $value
     * @param array $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        if (!$value instanceof PaymentMethod) {
            throw new InvalidArgumentException('The given value is not an PaymentMethod instance.');
        }

        $value = [
            'type' => $value->type,
            'data' => $value->data,
            'gateway_key' => $value->gateway_key
        ];

        return json_encode($value);
    }
}
