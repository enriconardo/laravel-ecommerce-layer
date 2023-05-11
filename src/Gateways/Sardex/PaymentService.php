<?php

namespace EcommerceLayer\Gateways\Sardex;

use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Gateways\Models\GatewayCustomer;
use EcommerceLayer\Gateways\PaymentServiceInterface;
use EcommerceLayer\Gateways\Models\GatewayPayment;
use EcommerceLayer\DomainModels\PaymentMethod;
use Illuminate\Support\Facades\Http;

class PaymentService implements PaymentServiceInterface
{
    public function create(
        int $amount,
        string $currency,
        PaymentMethod $paymentMethod,
        GatewayCustomer $customer = null,
        array $args = []
    ): GatewayPayment {
        $url = config('ecommerce-layer.gateways.sardex.api_url') . '/api/tickets';

        $apiUsername = config('ecommerce-layer.gateways.sardex.api_username');
        $apiPassword = config('ecommerce-layer.gateways.sardex.api_password');

        $response = Http::withOptions(['synchronous' => true])
            ->withBasicAuth($apiUsername, $apiPassword)
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
            'approval_url' => $body['approveUrl']
        ]);
    }

    public function confirm(GatewayPayment $payment, array $args = []): GatewayPayment
    {
        $paymentData = $payment->data();
        $ticketNumber = $paymentData['ticket_number'];
        $orderId = $paymentData['order_id'];

        $apiUsername = config('ecommerce-layer.gateways.sardex.api_username');
        $apiPassword = config('ecommerce-layer.gateways.sardex.api_password');

        $url = config('ecommerce-layer.gateways.sardex.api_url') . '/api/tickets/' . $ticketNumber . '/process?orderId=' . $orderId;
        
        $response = Http::withOptions(['synchronous' => true])
            ->withBasicAuth($apiUsername, $apiPassword)
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