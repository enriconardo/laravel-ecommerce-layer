<?php

namespace EnricoNardo\EcommerceLayer\Services;

use EnricoNardo\EcommerceLayer\Enums\PaymentStatus;
use EnricoNardo\EcommerceLayer\ModelBuilders\OrderBuilder;
use EnricoNardo\EcommerceLayer\Models\Order;

class OrderService
{
    public function pay(Order $order) : Order
    {
        if ($order->payment_status !== PaymentStatus::UNPAID) {
            // Only unpaid order can be paid
            return $order;
        }

        /** @var \EnricoNardo\EcommerceLayer\Models\Gateway $gateway */
        $gateway = $order->gateway;

        /** @var \EnricoNardo\EcommerceLayer\Gateways\GatewayServiceInterface $gatewayService */
        $gatewayService = gateway($gateway->identifier);

        /** @var \EnricoNardo\EcommerceLayer\Gateways\Models\Payment $payment */
        $payment = $gatewayService->payments()->createAndConfirm(
            $order->total,
            $order->currency->value,
            $order->payment_method,
            $order->customer->getGatewayCustomerIdentifier($gateway->identifier)
        );

        $order = OrderBuilder::init($order)->fill([
            'payment_status' => $payment->status,
            'gateway_payment_identifier' => $payment->identifier
        ])->end();

        return $order;
    }
}
