<?php

namespace EcommerceLayer\Models;

/**
 * @property string|null $type The type of the payment method, could by something like stripe, sofort...
 * @property array|null $data The set of data required for the payment method.
 * @property string|null $gateway_id Store the ID of the related payment method object returned by the gateway.
 */
class PaymentMethod
{
    public string|null $gateway_id;

    public string|null $type;

    public array $data;

    public function __construct(
        string $type = null,
        array $data = [],
        string $gatewayId = null
    ) {
        $this->type = $type;
        $this->data = $data;
        $this->gateway_id = $gatewayId;
    }
}
