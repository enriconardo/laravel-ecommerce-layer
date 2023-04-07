<?php

namespace EcommerceLayer\Models;

/**
 * @property string $type The type of the payment method, could by something like stripe, sofort...
 * @property array $data The set of data required for the payment method.
 * @property string|null $gateway_key Store the ID of the related payment method object returned by the gateway.
 */
class PaymentMethod
{
    public string|null $gateway_key;

    public string $type;

    public array $data;

    public function __construct(
        string $type,
        array $data = [],
        string $gatewayKey = null
    ) {
        $this->type = $type;
        $this->data = $data;
        $this->gateway_key = $gatewayKey;
    }
}
