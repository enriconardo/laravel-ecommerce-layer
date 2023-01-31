<?php

namespace EnricoNardo\EcommerceLayer\Models;

class PaymentMethod
{
    public string $type; // card, sofort...

    public array $data; // A set of data required for the payment method

    public function __construct(
        string $type,
        array $data = [],
    ) {
        $this->type = $type;
        $this->data = $data;
    }
}
