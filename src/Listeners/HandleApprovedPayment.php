<?php

namespace EcommerceLayer\Listeners;

use EcommerceLayer\Gateways\Events\GatewayWebhookCalled;
use EcommerceLayer\Models\Order;
use EcommerceLayer\Services\OrderService;

class HandleApprovedPayment
{
    protected OrderService $orderService;

    /**
     * Create the event listener.
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Handle the event.
     */
    public function handle(GatewayWebhookCalled $event): void
    {
        /** @var \EcommerceLayer\Gateways\Models\GatewayPayment $gatewayPayment */
        $gatewayPayment = $event->payment;
        /** @var \EcommerceLayer\Models\Order $order */
        $order = Order::where('payment_data->gateway_id', $gatewayPayment->id)->first();

        if ($order && $order->payment_data && $order->payment_data->approval_url) {
            // Order exists and it required 3DS auth
            $this->orderService->updatePayment($order, $gatewayPayment);
        }
    }
}
