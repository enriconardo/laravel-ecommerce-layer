<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Gateways\Models\GatewayCustomer;

interface CustomerServiceInterface
{
    public function create(string $email, array $args = []): GatewayCustomer;

    public function update(string $email, array $args = []): GatewayCustomer;

    public function findByEmail(string $email): GatewayCustomer|null;
}
