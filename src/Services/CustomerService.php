<?php

namespace EnricoNardo\EcommerceLayer\Services;

use EnricoNardo\EcommerceLayer\ModelBuilders\CustomerBuilder;
use EnricoNardo\EcommerceLayer\Models\Customer;
use EnricoNardo\EcommerceLayer\Models\Gateway;
use Illuminate\Support\Arr;

class CustomerService
{
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