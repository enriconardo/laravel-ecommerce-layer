<?php

namespace EcommerceLayer\Gateways\Stripe;

use EcommerceLayer\Gateways\CustomerServiceInterface;
use EcommerceLayer\Gateways\Models\GatewayCustomer;
use EcommerceLayer\Models\Address;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Stripe\StripeClient;

class CustomerService implements CustomerServiceInterface
{
    protected StripeClient $client;

    public function __construct(StripeClient $client)
    {
        $this->client = $client;
    }

    public function create(string $email, Address|null $address = null, array|null $metadata = null): GatewayCustomer
    {
        $gatewayCustomer = $this->findByEmail($email);

        if ($gatewayCustomer instanceof GatewayCustomer) {
            return $this->_update($gatewayCustomer->id, $address, $metadata);
        }

        return $this->_create($email, $address, $metadata);
    }

    public function update(string $email, Address|null $address = null, array|null $metadata = null): GatewayCustomer
    {
        $gatewayCustomer = $this->findByEmail($email);

        if (!$gatewayCustomer) {
            throw new ModelNotFoundException("Customer with email [$email] has not found in the gateway.");
        }

        return $this->_update($gatewayCustomer->id, $address, $metadata);
    }

    public function findByEmail(string $email): GatewayCustomer|null
    {
        $results = $this->client->customers->search([
            'query' => "email:'$email'",
            'limit' => 1
        ]);

        $stripeCustomer = count($results->data) > 0 ? $results->data[0] : null;

        return $stripeCustomer !== null ? new GatewayCustomer($stripeCustomer->id) : null;
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
        $stripeCustomer = $this->client->customers->create(attributes_filter([
            'email' => $email,
            'address' => ($address instanceof Address) ? $this->_getAddressData($address) : null,
            'metadata' => $metadata
        ]));

        return new GatewayCustomer($stripeCustomer->id);
    }

    protected function _update($id, Address|null $address = null, array|null $metadata = null)
    {
        $stripeCustomer = $this->client->customers->update($id, attributes_filter([
            'address' => ($address instanceof Address) ? $this->_getAddressData($address) : null,
            'metadata' => $metadata
        ]));

        return new GatewayCustomer($stripeCustomer->id);
    }
}
