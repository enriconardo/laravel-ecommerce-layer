<?php

namespace EcommerceLayer\Listeners;

use EcommerceLayer\Gateways\Events\GatewayPaymentUpdated;
use EcommerceLayer\Models\Order;
use EcommerceLayer\Services\OrderService;

class Handle3DSecurePayment
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
    public function handle(GatewayPaymentUpdated $event): void
    {
        /** @var \EcommerceLayer\Gateways\Models\GatewayPayment $gatewayPayment */
        $gatewayPayment = $event->payment;
        /** @var \EcommerceLayer\Models\Order $order */
        $order = Order::where('payment_data->gateway_id', $gatewayPayment->id)->first();

        if ($order && $order->payment_data && $order->payment_data->three_d_secure_redirect_url) {
            // Order exists and it required 3DS auth
            $this->orderService->updatePayment($order, $gatewayPayment);
        }
    }
}
