<?php

namespace EcommerceLayer\Gateways\Sardex;

use EcommerceLayer\Gateways\GatewayProviderInterface;
use EcommerceLayer\Gateways\PaymentServiceInterface;
use EcommerceLayer\Gateways\CustomerServiceInterface;
use EcommerceLayer\Gateways\PaymentMethodServiceInterface;

class Sardex implements GatewayProviderInterface
{
    private PaymentServiceInterface $payments;

    private CustomerServiceInterface $customers;

    private PaymentMethodServiceInterface $paymentMethods;

    public function getIdentifier(): string
    {
        return 'sardex';
    }

    public function payments(): PaymentServiceInterface
    {
        if (isset($this->payments)) {
            return $this->payments;
        }

        $this->payments = new PaymentService();
        return $this->payments;
    }

    public function customers(): CustomerServiceInterface
    {
        if (isset($this->customers)) {
            return $this->customers;
        }

        $this->customers = new CustomerService();
        return $this->customers;
    }

    public function paymentMethods(): PaymentMethodServiceInterface
    {
        if (isset($this->paymentMethods)) {
            return $this->paymentMethods;
        }

        $this->paymentMethods = new PaymentMethodService();
        return $this->paymentMethods;
    }
}
