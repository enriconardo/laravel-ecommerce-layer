<?php

namespace EcommerceLayer\Listeners;

use EcommerceLayer\Webhooks;

class CallWebhooks
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $class = get_class($event);

        switch ($class) {
            case \EcommerceLayer\Events\Order\OrderPlaced::class:
                /** @var \EcommerceLayer\Events\Order\OrderPlaced $event */
                $eventType = 'order.placed';
                $order = $event->order;
                $order->load('lineItems');
                $data = ['order' => $order];
                break;
            case \EcommerceLayer\Events\Order\OrderCompleted::class:
                /** @var \EcommerceLayer\Events\Order\OrderCompleted $event */
                $eventType = 'order.completed';
                $data = ['order' => $event->order];
                break;
            case \EcommerceLayer\Events\Order\OrderCanceled::class:
                /** @var \EcommerceLayer\Events\Order\OrderCanceled $event */
                $eventType = 'order.canceled';
                $data = ['order' => $event->order];
                break;
            case \EcommerceLayer\Events\Subscriptions\SubscriptionActivated::class:
                $eventType = 'subscription.activated';
                $data = ['subscription' => $event->subscription];
                break;
            case \EcommerceLayer\Events\Subscriptions\SubscriptionRenewed::class:
                $eventType = 'subscription.renewed';
                $data = ['subscription' => $event->subscription];
                break;
            case \EcommerceLayer\Events\Subscriptions\SubscriptionUnpaid::class:
                $eventType = 'subscription.unpaid';
                $data = ['subscription' => $event->subscription];
                break;
            case \EcommerceLayer\Events\Subscriptions\SubscriptionPending::class:
                $eventType = 'subscription.pending';
                $data = ['subscription' => $event->subscription];
                break;
            case \EcommerceLayer\Events\Subscriptions\SubscriptionCanceled::class:
                $eventType = 'subscription.canceled';
                $data = ['subscription' => $event->subscription];
                break;
            default:
                $eventType = null;
        }

        if ($event !== null) {
            Webhooks::call($eventType, $data);
        }
    }
}
