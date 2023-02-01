<?php

namespace EnricoNardo\EcommerceLayer\Models;

/**
 * @property string $type The type of the payment method, could by something like stripe, sofort...
 * @property array $data The set of data required for the payment method.
 */
class PaymentMethod
{
    public string $type;

    public array $data;

    public function __construct(
        string $type,
        array $data = [],
    ) {
        $this->type = $type;
        $this->data = $data;
    }
}
