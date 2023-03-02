<?php

namespace EcommerceLayer\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use EcommerceLayer\Models\PaymentMethod as PaymentMethodValueObject;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class PaymentMethod implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return PaymentMethodValueObject
     */
    public function get($model, $key, $value, $attributes)
    {
        $encodedValue = Arr::get($attributes, $key);
        $value = json_decode($encodedValue, true);
        return is_null($value) ? null : new PaymentMethodValueObject($value['type'], Arr::get($value, 'data', []));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param PaymentMethodValueObject $value
     * @param array $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        if (!$value instanceof PaymentMethodValueObject) {
            throw new InvalidArgumentException('The given value is not an PaymentMethod instance.');
        }

        $value = [
            'type' => $value->type,
            'data' => $value->data,
        ];

        return json_encode($value);
    }
}
