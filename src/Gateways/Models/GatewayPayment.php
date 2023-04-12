<?php

namespace EcommerceLayer\Gateways\Models;

use EcommerceLayer\Enums\PaymentStatus;

/**
 * @property string $id The id of the payment related object returned by the payment gateway API.
 * @property PaymentStatus $status
 * @property array $data A set of additional data useful for example to handle redirect urls.
 * @property string $three_d_secure_redirect_url
 */
class GatewayPayment
{
    public string $id;

    public PaymentStatus $status;

    private array $data;

    public function __construct(string $id, PaymentStatus $status)
    {
        $this->id = $id;
        $this->status = $status;
        $this->data = [];
    }

    public function __get(string $name)
    {
        if ($name === 'id' || $name === 'status' || $name === 'data') {
            return $this->$name;
        }

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }

    public function data()
    {
        return $this->data;
    }

    /**
     * If the payment requires 3DS authentication, set the 3DS url with this method.
     */
    public function setThreeDSecure($redirectUrl)
    {
        $this->data['three_d_secure_redirect_url'] = $redirectUrl;
    }
}