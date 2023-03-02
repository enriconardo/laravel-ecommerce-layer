<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Models\Address;
use EcommerceLayer\Gateways\Models\Customer;

interface CustomerServiceInterface
{
    public function create(string $email, Address|null $address = null, array|null $metadata = null): Customer;

    public function update(string $email, Address|null $address = null, array|null $metadata = null): Customer;

    public function findByEmail(string $email): Customer|null;
}
