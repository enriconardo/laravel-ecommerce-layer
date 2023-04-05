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

        $stripePaymentIntent = $this->client->paymentIntents->create(attributes_filter([
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $stripePaymentMethod->id,
            'customer' => $customerIdentifier
        ]));

        return $this->createPaymentObject($stripePaymentIntent);
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

        $stripePaymentIntent = $this->client->paymentIntents->create(attributes_filter([
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $stripePaymentMethod->id,
            'customer' => $customerIdentifier,
            'confirm' => true,
        ]));

        return $this->createPaymentObject($stripePaymentIntent);
    }

    public function confirm(Payment $payment): Payment
    {
        $stripePaymentIntent = $this->client->paymentIntents->retrieve($payment->identifier);
        $stripePaymentIntent = $stripePaymentIntent->confirm();

        return $this->createPaymentObject($stripePaymentIntent);
    }

    /**
     * Transform a Stripe status in a Ecommerce Layer internal payment status.
     * 
     * @return PaymentStatus
     */
    protected function getStatus(PaymentIntent $stripePaymentIntent)
    {
        switch ($stripePaymentIntent->status) {
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

    protected function createPaymentObject(PaymentIntent $paymentIntent)
    {
        $additionalData = [];

        // Manage payment intent next action
        if ($paymentIntent->next_action && $paymentIntent->next_action->use_stripe_sdk->type === 'three_d_secure_redirect') {
            $additionalData = [
                'three_d_secure_redirect' => $paymentIntent->next_action->use_stripe_sdk->stripe_js
            ];
        }

        return new Payment(
            $paymentIntent->id,
            $this->getStatus($paymentIntent),
            $additionalData
        );
    }
}
