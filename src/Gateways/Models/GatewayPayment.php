<?php

namespace EcommerceLayer\Gateways\Models;

use EcommerceLayer\Enums\PaymentStatus;

/**
 * @property string $id The id of the payment related object returned by the payment gateway API.
 * @property PaymentStatus $status
 * @property array $data A set of additional data useful for example to handle redirect urls.
 */
class GatewayPayment
{
    public string $id;

    public PaymentStatus $status;

    private array $data;

    public function __construct(string $id, PaymentStatus $status, $data = [])
    {
        $this->id = $id;
        $this->status = $status;
        $this->data = $data;
    }

    public function __get(string $name)
    {
        if ($name === 'id' || $name === 'status' || $name === 'data') {
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
}