<?php

namespace EcommerceLayer\Gateways\Stripe;

use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Gateways\Models\GatewayCustomer;
use EcommerceLayer\Gateways\PaymentServiceInterface;
use EcommerceLayer\Gateways\Models\GatewayPayment;
use EcommerceLayer\Models\PaymentMethod;
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
        GatewayCustomer $customer = null,
        array $args = []
    ): GatewayPayment {
        // Set attributes
        $attributes = [
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $paymentMethod->gateway_id,
            'confirm' => true
        ];

        if ($customer) {
            $attributes['customer'] = $customer->id;
        }

        if (array_key_exists('off_session', $args) && $args['off_session']) {
            $attributes['off_session'] = $args['off_session'];
        } else {
            $attributes['setup_future_usage'] = 'off_session';
        }

        if (array_key_exists('return_url', $args) && $args['return_url']) {
            $attributes['return_url'] = $args['return_url'];
        }
        // End of attributes setting

        try {
            $stripePaymentIntent = $this->client->paymentIntents->create($attributes);
        } catch (CardException $e) {
            $stripePaymentIntent = $this->_handleException($e);
        }

        return $this->_createPaymentObject($stripePaymentIntent);
    }

    public function confirm(GatewayPayment $payment, array $args = []): GatewayPayment
    {
        $stripePaymentIntent = $this->client->paymentIntents->retrieve($payment->key);
        $stripePaymentIntent = $stripePaymentIntent->confirm();

        return $this->_createPaymentObject($stripePaymentIntent);
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

    protected function _createPaymentObject(PaymentIntent $paymentIntent)
    {
        $data = [];

        // Manage payment intent 3DS auth
        if ($paymentIntent->next_action && $paymentIntent->next_action->type === 'redirect_to_url') {
            $data['approval_url'] = $paymentIntent->next_action->redirect_to_url->url;
        }

        return new GatewayPayment(
            $paymentIntent->id,
            $this->getStatus($paymentIntent),
            $data
        );
    }

    protected function _handleException(CardException $e)
    {
        $errorBody = $e->getJsonBody();

        if (array_key_exists('error', $errorBody) && array_key_exists('payment_intent', $errorBody['error'])) {
            $paymentIntentAsArray = $errorBody['error']['payment_intent'];
            return $this->client->paymentIntents->retrieve($paymentIntentAsArray['id']);
        }

        throw $e;
    }
}
