<?php

namespace EcommerceLayer\Listeners;

use Carbon\Carbon;
use EcommerceLayer\Enums\SubscriptionStatus;
use EcommerceLayer\Events\Order\OrderPaid;
use EcommerceLayer\Models\Order;
use EcommerceLayer\Models\LineItem;
use EcommerceLayer\Models\Price;
use EcommerceLayer\Models\Plan;
use EcommerceLayer\Services\SubscriptionService;

class CreateSubscription
{
    protected SubscriptionService $subscriptionService;

    /**
     * Create the event listener.
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        /** @var Order $order */
        $order = $event->order;

        $now = Carbon::now();

        $lineItems = $order->line_items;
        foreach ($lineItems as $lineItem) {
            /** 
             * @var LineItem $lineItem 
             * @var Price $price
             */
            $price = $lineItem->price;

            if (!$price->recurring) {
                continue;
            }

            /** @var Plan $plan */
            $plan = $price->plan;

            $this->subscriptionService->create([
                'customer_id' => $order->customer_id,
                'price_id' => $price->id,
                'status' => SubscriptionStatus::ACTIVE,
                'started_at' => $now,
                'expires_at' => $plan->calcExpirationTime($now)
            ]);
        }
    }
}
