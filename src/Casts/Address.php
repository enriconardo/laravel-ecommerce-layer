<?php

namespace EnricoNardo\EcommerceLayer\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use EnricoNardo\EcommerceLayer\Models\Address as AddressValueObject;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class Address implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return AddressValueObject
     */
    public function get($model, $key, $value, $attributes)
    {
        $encodedValue = Arr::get($attributes, $key);
        $value = json_decode($encodedValue, true);
        return is_null($value) ? null : new AddressValueObject($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param AddressValueObject $value
     * @param array $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        if (!$value instanceof AddressValueObject) {
            throw new InvalidArgumentException('The given value is not an Address instance.');
        }

        $value = [
            'address_line_1' => $value->address_line_1,
            'address_line_2' => $value->address_line_2,
            'postal_code' => $value->postal_code,
            'city' => $value->city,
            'state' => $value->state,
            'country' => $value->country,
            'fullname' => $value->fullname,
            'phone' => $value->phone,
        ];

        return json_encode($value);
    }
}
