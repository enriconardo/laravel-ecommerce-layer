<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Gateways\Models\GatewayCustomer;

interface CustomerServiceInterface
{
    public function create(string $email, array $args = []): GatewayCustomer|null;

    public function update(string $email, array $args = []): GatewayCustomer|null;

    public function find($id): GatewayCustomer|null;

    public function findByEmail(string $email): GatewayCustomer|null;
}
