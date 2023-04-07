<?php

namespace EcommerceLayer\Gateways\Models;

use EcommerceLayer\Enums\PaymentStatus;

/**
 * @property string $key The id of the payment related object returned by the payment gateway API.
 * @property PaymentStatus $status
 * @property array $data A set of additional data useful for example to handle redirect urls.
 */
class Payment
{
    public string $key;

    public PaymentStatus $status;

    private array $data;

    public function __construct(string $key, PaymentStatus $status, $args = [])
    {
        $this->key = $key;
        $this->status = $status;
        $this->data = [];

        foreach($args as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    public function __set(string $name, mixed $value)
    {
        if ($name === 'key' || $name === 'status') {
            $this->$name = $value;
        } else {
            $this->data[$name] = $value;
        }
    }

    public function __get(string $name)
    {
        if ($name === 'key' || $name === 'status') {
            return $this->$name;
        }

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }

    public function data()
    {
        return $this->data;
    }

    /**
     * If the payment requires 3DS authentication, set the 3DS url with this method.
     */
    public function setThreeDSecure($redirectUrl)
    {
        $this->data['three_d_secure_redirect_url'] = $redirectUrl;
    }
}