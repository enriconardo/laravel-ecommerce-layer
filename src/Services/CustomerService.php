<?php

namespace EcommerceLayer\Services;

use EcommerceLayer\Events\Entity\EntityCreated;
use EcommerceLayer\Events\Entity\EntityDeleted;
use EcommerceLayer\Events\Entity\EntityUpdated;
use EcommerceLayer\ModelBuilders\CustomerBuilder;
use EcommerceLayer\Models\Customer;
use EcommerceLayer\Models\Gateway;
use Illuminate\Support\Arr;

class CustomerService
{
    public function create(array $data): Customer
    {
        $attributes = [
            'email' => Arr::get($data, 'email'),
            'metadata' => Arr::get($data, 'metadata'),
        ];

        $customer = CustomerBuilder::init()->fill($attributes)->end();

        EntityCreated::dispatch($customer);

        return $customer;
    }

    public function update(Customer $customer, array $data): Customer
    {
        $attributes = [
            'metadata' => Arr::get($data, 'metadata'),
        ];

        $customer = CustomerBuilder::init($customer)->fill($attributes)->end();

        EntityUpdated::dispatch($customer);

        return $customer;
    }

    public function delete(Customer $customer)
    {
        $customer->delete();

        EntityDeleted::dispatch($customer);
    }

    public function syncWithGateway(Customer $customer, Gateway $gateway): Customer
    {
        /** @var \EcommerceLayer\Gateways\GatewayProviderInterface $gatewayService */
        $gatewayService = gateway($gateway->identifier);

        /** @var \EcommerceLayer\Gateways\Models\Customer $gatewayCustomer */
        $gatewayCustomer = $gatewayService->customers()->create($customer->email);

        $currentIdentifiers = $customer->gateway_customer_identifiers;

        Arr::set($currentIdentifiers, $gateway->identifier, $gatewayCustomer->identifier);

        return CustomerBuilder::init($customer)->fill([
            'gateway_customer_identifiers' => $currentIdentifiers
        ])->end();
    }
}