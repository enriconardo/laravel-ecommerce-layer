<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Gateways\Models\GatewayCustomer;
use EcommerceLayer\Gateways\Models\GatewayPayment;
use EcommerceLayer\Models\PaymentMethod;

interface PaymentServiceInterface
{
    public function create(
        int $amount,
        string $currency,
        PaymentMethod $paymentMethod,
        GatewayCustomer $customer = null,
        array $args = []
    ): GatewayPayment;

    public function confirm(GatewayPayment $payment, array $args = []): GatewayPayment;
}
