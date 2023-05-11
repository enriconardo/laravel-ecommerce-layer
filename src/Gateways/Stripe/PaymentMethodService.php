<?php

namespace EcommerceLayer\Gateways\Stripe;

use EcommerceLayer\Gateways\Models\GatewayPaymentMethod;
use EcommerceLayer\Gateways\PaymentMethodServiceInterface;
use Stripe\StripeClient;

class PaymentMethodService implements PaymentMethodServiceInterface
{
    protected StripeClient $client;

    public function __construct(StripeClient $client)
    {
        $this->client = $client;
    }

    public function create(string $type = null, array $data = []): GatewayPaymentMethod
    {
        $stripePaymentMethod = $this->client->paymentMethods->create([
            'type' => $type,
            $type => $data
        ]);

        $type = $stripePaymentMethod->type;
        return new GatewayPaymentMethod($type, $stripePaymentMethod->$type->toArray(), $stripePaymentMethod->id);
    }

    public function find(string $id): GatewayPaymentMethod|null
    {
        $stripePaymentMethod = $this->client->paymentMethods->retrieve($id);

        if (!$stripePaymentMethod) {
            return null;
        }

        $type = $stripePaymentMethod->type;
        return new GatewayPaymentMethod($type, $stripePaymentMethod->$type->toArray(), $stripePaymentMethod->id);
    }

}
