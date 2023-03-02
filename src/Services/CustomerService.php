<?php

namespace EnricoNardo\EcommerceLayer\Services;

use EnricoNardo\EcommerceLayer\Events\Customer\CustomerDeleted;
use EnricoNardo\EcommerceLayer\ModelBuilders\CustomerBuilder;
use EnricoNardo\EcommerceLayer\Models\Customer;
use EnricoNardo\EcommerceLayer\Models\Gateway;
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

        return $customer;
    }

    public function update(Customer $customer, array $data): Customer
    {
        $attributes = [
            'metadata' => Arr::get($data, 'metadata'),
        ];

        $customer = CustomerBuilder::init($customer)->fill($attributes)->end();

        return $customer;
    }

    public function delete(Customer $customer)
    {
        $customer->delete();

        CustomerDeleted::dispatch($customer);
    }

    public function syncWithGateway(Customer $customer, Gateway $gateway): Customer
    {
        /** @var \EnricoNardo\EcommerceLayer\Gateways\GatewayServiceInterface $gatewayService */
        $gatewayService = gateway($gateway->identifier);

        /** @var \EnricoNardo\EcommerceLayer\Gateways\Models\Customer $gatewayCustomer */
        $gatewayCustomer = $gatewayService->customers()->create($customer->email);

        $currentIdentifiers = $customer->gateway_customer_identifiers;

        Arr::set($currentIdentifiers, $gateway->identifier, $gatewayCustomer->identifier);

        return CustomerBuilder::init($customer)->fill([
            'gateway_customer_identifiers' => $currentIdentifiers
        ])->end();
    }
}