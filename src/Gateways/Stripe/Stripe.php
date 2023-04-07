<?php

namespace EcommerceLayer\Gateways\Stripe;

use EcommerceLayer\Gateways\GatewayProviderInterface;
use EcommerceLayer\Gateways\PaymentServiceInterface;
use EcommerceLayer\Gateways\CustomerServiceInterface;
use EcommerceLayer\Gateways\PaymentMethodServiceInterface;
use EcommerceLayer\Gateways\Stripe\PaymentMethodService;
use Stripe\StripeClient;

class Stripe implements GatewayProviderInterface
{
    private StripeClient $client;

    private PaymentServiceInterface $payments;

    private CustomerServiceInterface $customers;

    private PaymentMethodServiceInterface $paymentMethods;

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

    public function paymentMethods(): PaymentMethodServiceInterface
    {
        if (isset($this->paymentMethods)) {
            return $this->paymentMethods;
        }

        $this->paymentMethods = new PaymentMethodService($this->client);
        return $this->paymentMethods;
    }
}
