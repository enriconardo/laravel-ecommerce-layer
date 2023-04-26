<?php

namespace EcommerceLayer\Gateways\Stripe;

use EcommerceLayer\Gateways\CustomerServiceInterface;
use EcommerceLayer\Gateways\Models\GatewayCustomer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Stripe\StripeClient;

class CustomerService implements CustomerServiceInterface
{
    protected StripeClient $client;

    public function __construct(StripeClient $client)
    {
        $this->client = $client;
    }

    public function create(string $email, array $args = []): GatewayCustomer
    {
        $gatewayCustomer = $this->findByEmail($email);

        if ($gatewayCustomer instanceof GatewayCustomer) {
            return $this->_update($gatewayCustomer->id, $args);
        }

        return $this->_create($email, $args);
    }

    public function update(string $email, array $args = []): GatewayCustomer
    {
        $gatewayCustomer = $this->findByEmail($email);

        if (!$gatewayCustomer) {
            throw new ModelNotFoundException("Customer with email [$email] has not found in the gateway.");
        }

        return $this->_update($gatewayCustomer->id, $args);
    }

    public function find($id): GatewayCustomer|null
    {
        try {
            $stripeCustomer = $this->client->customers->retrieve($id);

            return new GatewayCustomer($stripeCustomer->id);
        } catch (Exception $e) {
            return null;
        }
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

    protected function _create(string $email, array $args = [])
    {
        $data = [
            'email' => $email
        ];

        if (array_key_exists('metadata', $args)) {
            $data['metadata'] = $args['metadata'];
        }

        $stripeCustomer = $this->client->customers->create($data);

        return new GatewayCustomer($stripeCustomer->id);
    }

    protected function _update($id, array $args = [])
    {
        $data = [];

        if (array_key_exists('metadata', $args)) {
            $data['metadata'] = $args['metadata'];
        }

        $stripeCustomer = $this->client->customers->update($id, $data);

        return new GatewayCustomer($stripeCustomer->id);
    }
}
