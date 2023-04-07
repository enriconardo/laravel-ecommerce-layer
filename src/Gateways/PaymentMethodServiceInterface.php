<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Gateways\Models\PaymentMethod as GatewayPaymentMethod;

interface PaymentMethodServiceInterface
{
    public function create(string $type, array $data): GatewayPaymentMethod;

    public function find(string $id): GatewayPaymentMethod|null;
}
