<?php

namespace EnricoNardo\EcommerceLayer\Services;

use EnricoNardo\EcommerceLayer\Enums\OrderStatus;
use EnricoNardo\EcommerceLayer\Exceptions\InvalidOrderException;
use EnricoNardo\EcommerceLayer\ModelBuilders\OrderBuilder;
use EnricoNardo\EcommerceLayer\Models\Order;

class OrderService
{
    public function place(Order $order, $confirm = false) : Order
    {
        // Order validation
        if ($order->status !== OrderStatus::DRAFT) {
            throw new InvalidOrderException("You cannot place an order that has been already placed");
        }

        if ($order->line_items->count() === 0) {
            throw new InvalidOrderException("You cannot place an empty order.");
        }

        if ($order->billing_address === null) {
            throw new InvalidOrderException("You cannot place an order without a billing address associated.");
        }

        if ($order->payment_method === null) {
            throw new InvalidOrderException("You cannot place an order without a payment method associated.");
        }
        // End fo order validation

        /** @var \EnricoNardo\EcommerceLayer\Gateways\GatewayServiceInterface $gatewayService */
        $gatewayService = gateway($order->gateway->identifier);

        $builder = OrderBuilder::init($order);

        $gatewayMethod = $confirm ? 'createAndConfirm' : 'create';
        /** @var \EnricoNardo\EcommerceLayer\Gateways\Models\Payment $payment */
        $payment = $gatewayService->payments()->$gatewayMethod(
            $order->total,
            $order->currency->value,
            $order->payment_method
        );

        $order = $builder->fill([
            'status' => OrderStatus::OPEN,
            'payment_status' => $payment->status,
            'gateway_payment_identifier' => $payment->identifier
        ])->end();

        return $order;
    }
}
