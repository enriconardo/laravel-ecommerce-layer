<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Gateways\Models\GatewayPayment;
use EcommerceLayer\Gateways\Models\GatewayPaymentMethod;

interface PaymentServiceInterface
{
    public function create(
        int $amount,
        string $currency,
        GatewayPaymentMethod $paymentMethod,
        array $args = []
    ): GatewayPayment;

    public function createAndConfirm(
        int $amount,
        string $currency,
        GatewayPaymentMethod $paymentMethod,
        array $args = []
    ): GatewayPayment;

    public function confirm(GatewayPayment $payment): GatewayPayment;
}
