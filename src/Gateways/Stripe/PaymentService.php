<?php

namespace EcommerceLayer\Gateways\Stripe;

use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Gateways\PaymentServiceInterface;
use EcommerceLayer\Gateways\Models\Payment;
use EcommerceLayer\Gateways\Models\PaymentMethod;
use Stripe\Exception\CardException;
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
        array $data = []
    ): Payment {
        $stripePaymentMethod = $this->client->paymentMethods->retrieve($paymentMethod->key);

        // Set attributes
        if (array_key_exists('customer_key', $data) && $data['customer_key']) {
            $data['customer'] = $data['customer_key'];
            unset($data['customer_key']);
        }

        $attributes = [
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $stripePaymentMethod->id,
            'setup_future_usage' => 'off_session',
            ...$data
        ];
        // End of attributes setting

        try {
            $stripePaymentIntent = $this->client->paymentIntents->create($attributes);
        } catch (CardException $e) {
            $errorBody = $e->getJsonBody();
            $paymentIntentAsArray = $errorBody['error']['payment_intent'];
            $stripePaymentIntent = $this->client->paymentIntents->retrieve($paymentIntentAsArray['id']);
        }

        return $this->createPaymentObject($stripePaymentIntent);
    }

    public function createAndConfirm(
        int $amount,
        string $currency,
        PaymentMethod $paymentMethod,
        array $data = []
    ): Payment {
        $stripePaymentMethod = $this->client->paymentMethods->retrieve($paymentMethod->key);

        // Set attributes
        if (array_key_exists('customer_key', $data) && $data['customer_key']) {
            $data['customer'] = $data['customer_key'];
            unset($data['customer_key']);
        }

        $attributes = [
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $stripePaymentMethod->id,
            'confirm' => true,
            'setup_future_usage' => 'off_session',
            ...$data
        ];
        // End of attributes setting

        try {
            $stripePaymentIntent = $this->client->paymentIntents->create($attributes);
        } catch (CardException $e) {
            $errorBody = $e->getJsonBody();
            $paymentIntentAsArray = $errorBody['error']['payment_intent'];
            $stripePaymentIntent = $this->client->paymentIntents->retrieve($paymentIntentAsArray['id']);
        }

        return $this->createPaymentObject($stripePaymentIntent);
    }

    public function confirm(Payment $payment): Payment
    {
        $stripePaymentIntent = $this->client->paymentIntents->retrieve($payment->key);
        $stripePaymentIntent = $stripePaymentIntent->confirm();

        return $this->createPaymentObject($stripePaymentIntent);
    }

    /**
     * Transform a Stripe status in a Ecommerce Layer internal payment status.
     * 
     * @return PaymentStatus
     */
    protected function getStatus(PaymentIntent $paymentIntent)
    {
        switch ($paymentIntent->status) {
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
        if ($paymentIntent->payment_method) {
            $additionalData['payment_method_key'] = $paymentIntent->payment_method;
        }

        $payment = new Payment(
            $paymentIntent->id,
            $this->getStatus($paymentIntent),
            $additionalData
        );

        // Manage payment intent next action
        if ($paymentIntent->next_action && $paymentIntent->next_action->type === 'redirect_to_url') {
            $payment->setThreeDSecure($paymentIntent->next_action->redirect_to_url->url);
        }

        return $payment;
    }
}
