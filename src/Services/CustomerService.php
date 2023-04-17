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
        $attributes = attributes_filter($data, ['email', 'metadata']);

        $customer = CustomerBuilder::init()->fill($attributes)->end();

        EntityCreated::dispatch($customer);

        return $customer;
    }

    public function update(Customer $customer, array $data): Customer
    {
        $attributes = attributes_filter($data, ['email', 'metadata']);

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

        /** @var \EcommerceLayer\Gateways\Models\GatewayCustomer $gatewayCustomer */
        $gatewayCustomer = $gatewayService->customers()->create($customer->email);

        $currentKeys = $customer->gateway_ids;

        Arr::set($currentKeys, $gateway->identifier, $gatewayCustomer->id);

        return CustomerBuilder::init($customer)->fill([
            'gateway_ids' => $currentKeys
        ])->end();
    }
}