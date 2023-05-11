<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\DomainModels\PaymentMethod;

interface PaymentMethodServiceInterface
{
    public function create(string $type = null, array $data = []): PaymentMethod;

    public function find(string $id): PaymentMethod|null;
}
