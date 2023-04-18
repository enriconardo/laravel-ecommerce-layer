<?php

namespace EcommerceLayer\Gateways;

class GatewayProviderFactory
{
    private array $enabledGateways = [];

    public function enableGateway(GatewayProviderInterface $gateway)
    {
        $this->enabledGateways[$gateway->getIdentifier()] = $gateway;
    }

    public function make(string $identifier): GatewayProviderInterface
    {
        if (is_array($this->enabledGateways) && array_key_exists($identifier, $this->enabledGateways)) {
            return $this->enabledGateways[$identifier];
        }

        return null;
    }

    // Call that class this way: app(GatewayProviderFactory::class)->make('stripe')
}