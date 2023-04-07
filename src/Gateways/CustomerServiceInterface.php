<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Models\Address;
use EcommerceLayer\Gateways\Models\GatewayCustomer;

interface CustomerServiceInterface
{
    public function create(string $email, Address|null $address = null, array|null $metadata = null): GatewayCustomer;

    public function update(string $email, Address|null $address = null, array|null $metadata = null): GatewayCustomer;

    public function findByEmail(string $email): GatewayCustomer|null;
}
