<?php

namespace EcommerceLayer\Models;

/**
 * @property string $gateway_key
 * @property array $attributes
 * @property string $three_d_secure_redirect_url
 */
class PaymentData
{
    public string $gateway_key;

    private array $attributes;

    public function __construct(
        string $gatewayKey,
        array $args = [],
    ) {
        $this->gateway_key = $gatewayKey;

        if (array_key_exists('three_d_secure_redirect_url', $args)) {
            $this->attributes['three_d_secure_redirect_url'] = $args['three_d_secure_redirect_url'];
        }
    }

    public function __set(string $name, mixed $value)
    {
        $this->attributes[$name] = $value;
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
}
