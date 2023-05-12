<?php

namespace EcommerceLayer\Casts;

use EcommerceLayer\Enums\PlanInterval;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use EcommerceLayer\Models\Plan;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class PlanCast implements CastsAttributes
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
        return is_null($value) 
            ? null 
            : new Plan(PlanInterval::from($value['interval']), $value['interval_count']);
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
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Plan) {
            throw new InvalidArgumentException('The given value is not an Plan instance.');
        }

        $values = [
            'interval' => $value->interval->value,
            'interval_count' => $value->interval_count,
        ];

        return json_encode(array_filter($values, function($v) {
            return $v !== null;
        }));
    }
}
