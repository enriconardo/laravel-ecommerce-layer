<?php

namespace EcommerceLayer\Gateways\Models;

/**
 * @property string|null $id The id of the payment method returned by the payment gateway API.
 * @property string $type The type of the payment method
 * @property array $data The set of data required for the payment method.
 */
class GatewayPaymentMethod
{
    public string|null $id;

    public string $type;

    public array $data;

    public function __construct(string $type, array $data = [], string $id = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->data = $data;
    }
}