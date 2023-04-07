<?php

namespace EcommerceLayer\Gateways\Models;

/**
 * @property string $id The id of the customer related object returned by the gateway API.
 */
class GatewayCustomer
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}