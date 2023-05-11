<?php

namespace EcommerceLayer\Gateways\Sardex;

use EcommerceLayer\Gateways\Models\GatewayPaymentMethod;
use EcommerceLayer\Gateways\PaymentMethodServiceInterface;

class PaymentMethodService implements PaymentMethodServiceInterface
{
    public function create(string $type = null, array $data = []): GatewayPaymentMethod
    {
        return new GatewayPaymentMethod($type, $data);
    }

    public function find(string $id): GatewayPaymentMethod|null
    {
        return null;
    }
}
