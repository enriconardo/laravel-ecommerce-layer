<?php

namespace EcommerceLayer\Gateways\Models;

use EcommerceLayer\Enums\PaymentStatus;

/**
 * @property string $id A unique value needed to identify the payment data of a specific order. Could be an id returned by the gateway.
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