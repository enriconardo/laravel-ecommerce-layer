<?php

namespace EnricoNardo\EcommerceLayer\Gateways;

interface GatewayServiceInterface
{
    public function getIdentifier(): string;

    public function payments(): PaymentServiceInterface;

    public function customers(): CustomerServiceInterface;
}
