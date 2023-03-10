<?php

namespace EcommerceLayer\Gateways\Models;

use EcommerceLayer\Enums\PaymentStatus;

/**
 * @property string $identifier The id of the payment related object returned by the payment gateway API.
 * @property PaymentStatus $status
 */
class Payment
{
    public string $identifier;

    public PaymentStatus $status;

    public function __construct(string $identifier, PaymentStatus $status)
    {
        $this->identifier = $identifier;
        $this->status = $status;
    }
}