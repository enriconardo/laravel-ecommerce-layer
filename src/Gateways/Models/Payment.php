<?php

namespace EcommerceLayer\Gateways\Models;

use EcommerceLayer\Enums\PaymentStatus;

/**
 * @property string $key The id of the payment related object returned by the payment gateway API.
 * @property PaymentStatus $status
 * @property array $data A set of additional data useful for example to handle redirect urls.
 */
class Payment
{
    public string $key;

    public PaymentStatus $status;

    private array $data;

    public function __construct(string $key, PaymentStatus $status)
    {
        $this->key = $key;
        $this->status = $status;
    }

    public function getData()
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