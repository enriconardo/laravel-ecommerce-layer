<?php

namespace EcommerceLayer\Gateways\Sardex;

use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Gateways\Models\GatewayCustomer;
use EcommerceLayer\Gateways\PaymentServiceInterface;
use EcommerceLayer\Gateways\Models\GatewayPayment;
use EcommerceLayer\Gateways\Models\GatewayPaymentMethod;
use Exception;
use Illuminate\Support\Facades\Http;

class PaymentService implements PaymentServiceInterface
{
    public function create(
        int $amount,
        string $currency,
        GatewayPaymentMethod $paymentMethod,
        GatewayCustomer $customer = null,
        array $args = []
    ): GatewayPayment {
        $url = config('ecommerce-layer.gateways.sardex.api_url') . '/api/tickets';

        $apiToken = config('ecommerce-layer.gateways.sardex.api_token');

        $response = Http::withOptions(['synchronous' => true])
            ->withHeaders([
                'Authorization' => 'Basic ' . $apiToken
            ])
            ->timeout(5)
            ->retry(1)
            ->post($url, [
                "orderId" => $args['order_id'],
                "amount" => $amount / 100, // $amount is expressed in cents but Sardex wants the value with decimals
                "description" => "",
                "successUrl" => array_key_exists('success_url', $args) ? $args['success_url'] : "",
                "successWebhook" => route('sardex.webhook'),
                "cancelUrl" => array_key_exists('success_url', $args) ? $args['cancel_url'] : "",
                "type" => "contoCC.accredito"
            ]);

        // Throw an exception if a client or server error occurred
        $response->throw();

        $body = $response->json();

        return new GatewayPayment($body['id'], PaymentStatus::PENDING, [
            'ticket_number' => $body['ticketNumber'],
            'transaction_number' => $body['transactionNumber']
        ]);
    }

    public function createAndConfirm(
        int $amount,
        string $currency,
        GatewayPaymentMethod $paymentMethod,
        GatewayCustomer $customer = null,
        array $args = []
    ): GatewayPayment {
        return $this->create($amount, $currency, $paymentMethod, $customer, $args);
    }

    public function confirm(GatewayPayment $payment, array $args = []): GatewayPayment
    {
        $paymentData = $payment->data();
        $ticketNumber = $paymentData['ticket_number'];
        $orderId = $paymentData['order_id'];

        $apiToken = config('ecommerce-layer.gateways.sardex.api_token');

        $url = config('ecommerce-layer.gateways.sardex.api_url') . '/api/tickets/' . $ticketNumber . '/process?orderId=' . $orderId;
        
        $response = Http::withOptions(['synchronous' => true])
            ->withHeaders([
                'Authorization' => 'Basic ' . $apiToken
            ])
            ->timeout(5)
            ->retry(1)
            ->post($url);

        // Throw an exception if a client or server error occurred
        $response->throw();

        $body = $response->json();

        $status = $body['actuallyProcessed'] ? PaymentStatus::PAID : PaymentStatus::REFUSED;

        $payment->status = $status;

        return $payment;
    }
}
