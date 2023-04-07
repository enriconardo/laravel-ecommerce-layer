<?php

namespace EcommerceLayer\Gateways;

interface GatewayProviderInterface
{
    public function getIdentifier(): string;

    public function payments(): PaymentServiceInterface;

    public function customers(): CustomerServiceInterface;

    public function paymentMethods(): PaymentMethodServiceInterface;
}
