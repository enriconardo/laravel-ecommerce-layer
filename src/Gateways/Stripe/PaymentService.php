<?php

namespace EcommerceLayer\Gateways\Stripe;

use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Gateways\PaymentServiceInterface;
use EcommerceLayer\Gateways\Models\Payment;
use EcommerceLayer\Models\PaymentMethod;
use Stripe\StripeClient;
use Stripe\PaymentIntent;

class PaymentService implements PaymentServiceInterface
{
    protected StripeClient $client;

    public function __construct(StripeClient $client)
    {
        $this->client = $client;
    }

    public function create(
        int $amount,
        string $currency,
        PaymentMethod $paymentMethod,
        string $customerIdentifier = null
    ): Payment {
        $type = $paymentMethod->type;

        $stripePaymentMethod = $this->client->paymentMethods->create([
            'type' => $type,
            $type => $paymentMethod->data,
        ]);

        $stripePymentIntent = $this->client->paymentIntents->create(attributes_filter([
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $stripePaymentMethod->id,
            'customer' => $customerIdentifier
        ]));

        return new Payment(
            $stripePymentIntent->id,
            $this->getStatus($stripePymentIntent)
        );
    }

    public function createAndConfirm(
        int $amount,
        string $currency,
        PaymentMethod $paymentMethod,
        string $customerIdentifier = null
    ): Payment {
        $type = $paymentMethod->type;

        $stripePaymentMethod = $this->client->paymentMethods->create([
            'type' => $type,
            $type => $paymentMethod->data,
        ]);

        $stripePymentIntent = $this->client->paymentIntents->create(attributes_filter([
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $stripePaymentMethod->id,
            'customer' => $customerIdentifier,
            'confirm' => true
        ]));

        return new Payment(
            $stripePymentIntent->id,
            $this->getStatus($stripePymentIntent)
        );
    }

    public function confirm(Payment $payment): Payment
    {
        $stripePymentIntent = $this->client->paymentIntents->retrieve($payment->identifier);
        $stripePymentIntent = $stripePymentIntent->confirm();

        return new Payment(
            $stripePymentIntent->id,
            $this->getStatus($stripePymentIntent)
        );
    }

    /**
     * Transform a Stripe status in a Ecommerce Layer internal payment status.
     * 
     * @return PaymentStatus
     */
    protected function getStatus(PaymentIntent $stripePymentIntent)
    {
        switch ($stripePymentIntent->status) {
            case 'requires_action':
            case 'processing':
                return PaymentStatus::PENDING;
            case 'requires_confirmation':
                return PaymentStatus::AUTHORIZED;
            case 'succeeded':
                return PaymentStatus::PAID;
            case 'canceled':
                return PaymentStatus::VOIDED;
            default:
                return PaymentStatus::REFUSED;
        }
    }
}
