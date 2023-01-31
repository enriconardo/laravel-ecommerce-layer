<?php

namespace EnricoNardo\EcommerceLayer\Models;

use EnricoNardo\EcommerceLayer\Enums\PaymentStatus;

class Payment
{
    public string|null $gateway_identifier;

    public PaymentStatus $status;

    public function __construct(PaymentStatus $status, string|null $gatewayIdentifier = null)
    {
        $this->gateway_identifier = $gatewayIdentifier;
        $this->status = $status;
    }
}