<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Models\PaymentMethod;

interface PaymentMethodServiceInterface
{
    public function create(string $type = null, array $data = []): PaymentMethod;

    public function find(string $id): PaymentMethod|null;
}
