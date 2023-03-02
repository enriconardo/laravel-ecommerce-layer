<?php

namespace EcommerceLayer\Gateways\Models;

/**
 * @property string $identifier The id of the customer related object returned by the gateway API.
 */
class Customer
{
    public string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }
}