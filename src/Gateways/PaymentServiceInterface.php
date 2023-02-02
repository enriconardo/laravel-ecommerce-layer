<?php

namespace EnricoNardo\EcommerceLayer\Gateways;

use EnricoNardo\EcommerceLayer\Models\Address;
use EnricoNardo\EcommerceLayer\Gateways\Models\Payment;
use EnricoNardo\EcommerceLayer\Models\PaymentMethod;

interface PaymentServiceInterface
{
    public function create(int $amount, string $currency, PaymentMethod $paymentMethod, Address $billingAddress = null) : Payment;

    public function capture();
}
