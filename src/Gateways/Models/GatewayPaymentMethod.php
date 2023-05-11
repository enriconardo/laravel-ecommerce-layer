<?php

namespace EcommerceLayer\Gateways\Models;

/**
 * @property string|null $type The type of the payment method
 * @property array $data The set of data required for the payment method.
 * @property string|null $id The id of the payment method returned by the payment gateway API.
 */
class GatewayPaymentMethod
{
    public string|null $id;

    public string|null $type;

    public array $data;

    public function __construct(string $type = null, array $data = [], string $id = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->data = $data;
    }
}