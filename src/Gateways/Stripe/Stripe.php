<?php

namespace EcommerceLayer\Gateways\Stripe;

use EcommerceLayer\Gateways\GatewayServiceInterface;
use EcommerceLayer\Gateways\PaymentServiceInterface;
use EcommerceLayer\Gateways\CustomerServiceInterface;
use Stripe\StripeClient;

class Stripe implements GatewayServiceInterface
{
    private StripeClient $client;

    private PaymentServiceInterface $payments;

    private CustomerServiceInterface $customers;

    public function __construct()
    {
        $this->client = new StripeClient([
            'api_key' => config('ecommerce-layer.gateways.stripe.secret_key')
        ]);
    }

    public function getIdentifier(): string
    {
        return 'stripe';
    }

    public function payments(): PaymentServiceInterface
    {
        if (isset($this->payments)) {
            return $this->payments;
        }

        $this->payments = new PaymentService($this->client);
        return $this->payments;
    }

    public function customers(): CustomerServiceInterface
    {
        if (isset($this->customers)) {
            return $this->customers;
        }

        $this->customers = new CustomerService($this->client);
        return $this->customers;
    }
}
