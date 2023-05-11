<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Gateways\Models\GatewayCustomer;
use EcommerceLayer\Gateways\Models\GatewayPayment;
use EcommerceLayer\Gateways\Models\GatewayPaymentMethod;

interface PaymentServiceInterface
{
    public function create(
        int $amount,
        string $currency,
        GatewayPaymentMethod $paymentMethod,
        GatewayCustomer $customer = null,
        array $args = []
    ): GatewayPayment;

    public function confirm(GatewayPayment $payment, array $args = []): GatewayPayment;
}
