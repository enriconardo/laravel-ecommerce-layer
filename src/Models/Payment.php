<?php

namespace EnricoNardo\EcommerceLayer\Models;

use EnricoNardo\EcommerceLayer\Enums\PaymentStatus;

/**
 * @property string|null $gateway_identifier The id of the payment related object returned by the payment gateway API.
 * @property PaymentStatus $status
 */
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