<?php

namespace EcommerceLayer\Gateways;

use Illuminate\Support\Arr;

class GatewayProviderFactory
{
    private array $enabledGateways = [];

    public function enableGateway(GatewayProviderInterface $gateway)
    {
        $this->enabledGateways[$gateway->getIdentifier()] = $gateway;
    }

    public function make(string $identifier): GatewayProviderInterface
    {
        return Arr::get($this->enabledGateways, $identifier);
    }

    // Call that class this way: app(GatewayProviderFactory::class)->make('stripe')
}