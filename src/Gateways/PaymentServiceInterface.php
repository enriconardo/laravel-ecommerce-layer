<?php

namespace EcommerceLayer\Gateways;

use EcommerceLayer\Gateways\Models\Payment as GatewayPayment;
use EcommerceLayer\Models\PaymentMethod;

interface PaymentServiceInterface
{
    public function create(
        int $amount,
        string $currency,
        PaymentMethod $paymentMethod,
        array $data = []
    ): GatewayPayment;

    public function createAndConfirm(
        int $amount,
        string $currency,
        PaymentMethod $paymentMethod,
        array $data = []
    ): GatewayPayment;

    public function confirm(GatewayPayment $payment): GatewayPayment;
}
