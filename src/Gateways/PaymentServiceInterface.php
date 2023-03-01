<?php

namespace EnricoNardo\EcommerceLayer\Gateways;

use EnricoNardo\EcommerceLayer\Gateways\Models\Payment;
use EnricoNardo\EcommerceLayer\Models\PaymentMethod;

interface PaymentServiceInterface
{
    public function create(
        int $amount,
        string $currency,
        PaymentMethod $paymentMethod,
        string $customerIdentifier = null
    ): Payment;

    public function createAndConfirm(
        int $amount,
        string $currency,
        PaymentMethod $paymentMethod,
        string $customerIdentifier = null
    ): Payment;

    public function confirm(Payment $payment): Payment;
}
