<?php

namespace EnricoNardo\EcommerceLayer\Gateways\Stripe;

use EnricoNardo\EcommerceLayer\Gateways\CustomerServiceInterface;
use Stripe\StripeClient;

class CustomerService implements CustomerServiceInterface
{
    protected StripeClient $client;

    public function __construct(StripeClient $client)
    {
        $this->client = $this->client;
    }

    public function create($email, $billingAddress = null, $metadata = null)
    {
        // TODO
    }

    public function update($email, $billingAddress = null, $metadata = null)
    {
        // TODO
    }

    public function findByEmail($email)
    {
        $results = $this->client->customers->search([
            'query' => "email:'$email'",
            'limit' => 1
        ]);

        return count($results->data) > 0 ? $results->data[0] : null;
    }
}
