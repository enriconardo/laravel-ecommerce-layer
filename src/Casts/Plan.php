<?php

namespace EnricoNardo\EcommerceLayer\Casts;

use EnricoNardo\EcommerceLayer\Enums\PlanInterval;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use EnricoNardo\EcommerceLayer\Models\Plan as PlanValueObject;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class Plan implements CastsAttributes
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
        return is_null($value) ? null : new PlanValueObject(PlanInterval::from($value['interval']), $value['interval_count']);
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
        if (!$value instanceof PlanValueObject) {
            throw new InvalidArgumentException('The given value is not an Plan instance.');
        }

        $value = [
            'interval' => $value->interval->value,
            'interval_count' => $value->interval_count,
        ];

        return json_encode($value);
    }
}
