<?php

namespace EnricoNardo\EcommerceLayer\Gateways;

use EnricoNardo\EcommerceLayer\Models\Address;
use EnricoNardo\EcommerceLayer\Gateways\Models\Customer;

interface CustomerServiceInterface
{
    public function create(string $email, Address|null $address = null, array|null $metadata = null): Customer;

    public function update(string $email, Address|null $address = null, array|null $metadata = null): Customer;

    public function findByEmail(string $email): Customer|null;
}
