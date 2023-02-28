<?php

namespace EnricoNardo\EcommerceLayer\Services;

use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Exceptions\InvalidOrderException;
use EnricoNardo\EcommerceLayer\ModelBuilders\OrderBuilder;
use EnricoNardo\EcommerceLayer\Models\Order;
use Exception;

class OrderService
{
    public function pay(Order $order) : Order
    {
        if ($order->payment_status !== 'unpaid') {
            // Only unpaid order can be paid
            return $order;
        }

        /** @var \EnricoNardo\EcommerceLayer\Gateways\GatewayServiceInterface $gatewayService */
        $gatewayService = gateway($order->gateway->identifier);

        /** @var \EnricoNardo\EcommerceLayer\Gateways\Models\Payment $payment */
        $payment = $gatewayService->payments()->createAndConfirm(
            $order->total,
            $order->currency->value,
            $order->payment_method
        );

        $order = OrderBuilder::init($order)->fill([
            'payment_status' => $payment->status,
            'gateway_payment_identifier' => $payment->identifier
        ])->end();

        return $order;
    }
}
