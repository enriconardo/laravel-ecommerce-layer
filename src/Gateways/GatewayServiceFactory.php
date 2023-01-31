<?php

namespace EnricoNardo\EcommerceLayer\Gateways;

use Illuminate\Support\Arr;

class GatewayServiceFactory
{
    private array $enabledGateways = [];

    public function enableGateway(GatewayServiceInterface $gateway)
    {
        $this->enabledGateways[$gateway->getIdentifier()] = $gateway;
    }

    public function make(string $identifier): GatewayServiceInterface
    {
        return Arr::get($this->enabledGateways, $identifier);
    }

    // Call that class this way: app(GatewayServiceFactory::class)->make('stripe')
}