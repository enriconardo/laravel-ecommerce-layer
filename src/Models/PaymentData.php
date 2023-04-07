<?php

namespace EcommerceLayer\Models;

use JsonSerializable;

/**
 * @property string $gateway_id
 * @property array $attributes
 * @property string $three_d_secure_redirect_url
 */
class PaymentData implements JsonSerializable
{
    public string $gateway_id;

    private array $attributes;

    public function __construct(
        string $gatewayKey,
        array $args = [],
    ) {
        $this->gateway_id = $gatewayKey;
        $this->attributes = [];

        foreach($args as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    public function __set(string $name, mixed $value)
    {
        if ($name === 'gateway_id') {
            $this->gateway_id = $value;
        } else {
            $this->attributes[$name] = $value;
        }
    }

    public function __get(string $name)
    {
        if ($name === 'gateway_id') {
            return $this->gateway_id;
        }

        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return null;
    }

    public function attributes()
    {
        return $this->attributes;
    }

    public function __toArray()
    {
        $data = [
            'gateway_id' => $this->gateway_id,
        ];

        foreach ($this->attributes as $key => $attribute) {
            $data[$key] = $attribute;
        }

        return $data;
    }

    public function jsonSerialize(): mixed
    {
        return $this->__toArray();
    }
}
