<?php

namespace EnricoNardo\EcommerceLayer\Gateways\Stripe;

use EnricoNardo\EcommerceLayer\Enums\PaymentStatus;
use EnricoNardo\EcommerceLayer\Gateways\PaymentServiceInterface;
use EnricoNardo\EcommerceLayer\Models\Address;
use EnricoNardo\EcommerceLayer\Gateways\Models\Payment;
use EnricoNardo\EcommerceLayer\Models\PaymentMethod;
use Stripe\StripeClient;

class PaymentService implements PaymentServiceInterface
{
    protected StripeClient $client;

    public function __construct(StripeClient $client)
    {
        $this->client = $client;
    }

    public function create(int $amount, string $currency, PaymentMethod $paymentMethod, Address $billingAddress = null): Payment
    {
        $type = $paymentMethod->type;

        $stripePaymentMethod = $this->client->paymentMethods->create([
            'type' => $type,
            $type => $paymentMethod->data,
        ]);

        $stripePymentIntent = $this->client->paymentIntents->create([
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $stripePaymentMethod->id
        ]);

        $status = $stripePymentIntent->status === 'requires_payment_method' ? PaymentStatus::REFUSED : PaymentStatus::AUTHORIZED;

        return new Payment($stripePymentIntent->id, $status);
    }

    public function capture()
    {
        // TODO
    }
}
