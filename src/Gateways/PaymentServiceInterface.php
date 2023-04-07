<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Gateways\Models\Payment as GatewayPayment;
use EcommerceLayer\Gateways\Models\PaymentMethod as GatewayPaymentMethod;

interface PaymentServiceInterface
{
    public function create(
        int $amount,
        string $currency,
        GatewayPaymentMethod $paymentMethod,
        array $data = []
    ): GatewayPayment;

    public function createAndConfirm(
        int $amount,
        string $currency,
        GatewayPaymentMethod $paymentMethod,
        array $data = []
    ): GatewayPayment;

    public function confirm(GatewayPayment $payment): GatewayPayment;
}
