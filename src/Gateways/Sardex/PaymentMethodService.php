<?php

namespace EcommerceLayer\Gateways\Sardex;

use EcommerceLayer\DomainModels\PaymentMethod;
use EcommerceLayer\Gateways\PaymentMethodServiceInterface;

class PaymentMethodService implements PaymentMethodServiceInterface
{
    public function create(string $type = null, array $data = []): PaymentMethod
    {
        return new PaymentMethod();
    }

    public function find(string $id): PaymentMethod|null
    {
        return null;
    }
}
