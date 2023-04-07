<?php

namespace EcommerceLayer\Models;

use JsonSerializable;

/**
 * @property string $gateway_key
 * @property array $attributes
 * @property string $three_d_secure_redirect_url
 */
class PaymentData implements JsonSerializable
{
    public string $gateway_key;

    private array $attributes;

    public function __construct(
        string $gatewayKey,
        array $args = [],
    ) {
        $this->gateway_key = $gatewayKey;
        $this->attributes = [];

        foreach($args as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    public function __set(string $name, mixed $value)
    {
        if ($name === 'gateway_key') {
            $this->gateway_key = $value;
        } else {
            $this->attributes[$name] = $value;
        }
    }

    public function __get(string $name)
    {
        if ($name === 'gateway_key') {
            return $this->gateway_key;
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
            'gateway_key' => $this->gateway_key,
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
