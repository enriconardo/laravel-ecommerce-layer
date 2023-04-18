<?php

namespace EcommerceLayer\Gateways\Sardex;

use EcommerceLayer\Gateways\CustomerServiceInterface;
use EcommerceLayer\Gateways\Models\GatewayCustomer;

class CustomerService implements CustomerServiceInterface
{
    public function create(string $email, array $args = []): GatewayCustomer
    {
        return new GatewayCustomer($email);
    }

    public function update(string $email, array $args = []): GatewayCustomer
    {
        return new GatewayCustomer($email);
    }

    public function findByEmail(string $email): GatewayCustomer|null
    {
        return null;
    }
}
