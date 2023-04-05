<?php

namespace EcommerceLayer\Listeners;

use Carbon\Carbon;
use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Enums\SubscriptionStatus;
use EcommerceLayer\Events\Payment\PaymentUpdated;
use EcommerceLayer\Models\Order;
use EcommerceLayer\Models\LineItem;
use EcommerceLayer\Models\Price;
use EcommerceLayer\Models\Plan;
use EcommerceLayer\Models\Subscription;
use EcommerceLayer\Services\SubscriptionService;

class HandleSubscription
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
    public function handle(PaymentUpdated $event): void
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
            $expirationTime = $plan->calcExpirationTime($now);

            /** @var Subscription $sub */
            $subscription = Subscription::where('source_order_id', $order->id)->first();

            if (!$subscription && $order->payment_status === PaymentStatus::PAID) {
                // Subscription doesn't exists => create it
                $this->subscriptionService->create([
                    'customer_id' => $order->customer_id,
                    'price_id' => $price->id,
                    'status' => SubscriptionStatus::ACTIVE,
                    'started_at' => $now,
                    'expires_at' => $expirationTime,
                    'source_order_id' => $order->id
                ]);

                return;
            }

            // If subscription already exists
            switch ($order->payment_status) {
                case PaymentStatus::PAID:
                    $this->subscriptionService->update($subscription, [
                        'status' => SubscriptionStatus::ACTIVE,
                        'expires_at' => $expirationTime
                    ]);
                    break;
                case PaymentStatus::REFUSED:
                case PaymentStatus::VOIDED:
                    $this->subscriptionService->update($subscription, [
                        'status' => SubscriptionStatus::UNPAID
                    ]);
                    break;
            }

            // TODO handle other payment status
        }
    }
}
