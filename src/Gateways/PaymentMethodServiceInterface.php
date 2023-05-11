<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Gateways\Models\GatewayPaymentMethod;

interface PaymentMethodServiceInterface
{
    public function create(string $type = null, array $data = []): GatewayPaymentMethod;

    public function find(string $id): GatewayPaymentMethod|null;
}
