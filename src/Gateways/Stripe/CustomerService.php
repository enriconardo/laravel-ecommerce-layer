<?php

namespace EnricoNardo\EcommerceLayer\Gateways\Stripe;

use EnricoNardo\EcommerceLayer\Gateways\CustomerServiceInterface;
use EnricoNardo\EcommerceLayer\Gateways\Models\Customer;
use EnricoNardo\EcommerceLayer\Models\Address;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Stripe\StripeClient;

class CustomerService implements CustomerServiceInterface
{
    protected StripeClient $client;

    public function __construct(StripeClient $client)
    {
        $this->client = $client;
    }

    public function create(string $email, Address|null $address = null, array|null $metadata = null): Customer
    {
        $customer = $this->findByEmail($email);

        if ($customer instanceof Customer) {
            return $this->_update($email, $address, $metadata);
        }

        return $this->_create($email, $address, $metadata);
    }

    public function update(string $email, Address|null $address = null, array|null $metadata = null): Customer
    {
        $customer = $this->findByEmail($email);

        if (!$customer) {
            throw new ModelNotFoundException("Customer with email [$email] has not found in the gateway.");
        }

        return $this->_update($email, $address, $metadata);
    }

    public function findByEmail(string $email): Customer|null
    {
        $results = $this->client->customers->search([
            'query' => "email:'$email'",
            'limit' => 1
        ]);

        $gatewayCustomer = count($results->data) > 0 ? $results->data[0] : null;

        return $gatewayCustomer !== null ? new Customer($gatewayCustomer->id) : null;
    }

    protected function _getAddressData(Address $address)
    {
        return [
            'city' => $address->city,
            'country' => $address->country->value,
            'line1' => $address->address_line_1,
            'line2' => $address->address_line_2,
            'postal_code' => $address->postal_code,
            'state' => $address->state
        ];
    }

    protected function _create(string $email, Address|null $address = null, array|null $metadata = null)
    {
        $gatewayCustomer = $this->client->customers->create(attributes_filter([
            'email' => $email,
            'address' => ($address instanceof Address) ? $this->_getAddressData($address) : null,
            'metadata' => $metadata
        ]));

        return new Customer($gatewayCustomer->id);
    }

    protected function _update(string $email, Address|null $address = null, array|null $metadata = null)
    {
        $gatewayCustomer = $this->client->customers->update(attributes_filter([
            'address' => ($address instanceof Address) ? $this->_getAddressData($address) : null,
            'metadata' => $metadata
        ]));

        return new Customer($gatewayCustomer->id);
    }
}
