<?php

namespace EcommerceLayer\Gateways\Events;

use EcommerceLayer\Gateways\Models\GatewayPayment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This event could be useful to notify the application that a 3DS has been succesfully authorized.
 */
class GatewayPaymentUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GatewayPayment $payment;

    /**
     * Create a new event instance.
     */
    public function __construct(GatewayPayment $payment)
    {
        $this->payment = $payment;
    }
}
