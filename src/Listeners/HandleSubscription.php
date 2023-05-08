<?php

namespace EcommerceLayer\Listeners;

use Carbon\Carbon;
use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Enums\SubscriptionStatus;
use EcommerceLayer\Events\Payment\PaymentUpdated;
use EcommerceLayer\Events\Subscriptions\SubscriptionActivated;
use EcommerceLayer\Events\Subscriptions\SubscriptionPending;
use EcommerceLayer\Events\Subscriptions\SubscriptionRenewed;
use EcommerceLayer\Events\Subscriptions\SubscriptionUnpaid;
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

            /** @var Subscription $subscription */
            $subscription = Subscription::where('source_order_id', $order->id)
                ->where('price_id', $price->id)
                ->first();

            if (!$subscription && $order->payment_status === PaymentStatus::PAID) {
                // Subscription doesn't exists => create it
                $subscription = $this->subscriptionService->create([
                    'customer_id' => $order->customer_id,
                    'price_id' => $price->id,
                    'status' => SubscriptionStatus::ACTIVE,
                    'started_at' => $now,
                    'expires_at' => $plan->calcExpirationTime($now),
                    'source_order_id' => $order->id
                ]);

                SubscriptionActivated::dispatch($subscription);

                continue;
            } else if (!$subscription) {
                // If the payment hasn't been paid yet and the subscription does not exist => do nothing for the moment
                continue;
            }

            // If subscription already exists
            switch ($order->payment_status) {
                case PaymentStatus::PAID:
                    $subscription = $this->subscriptionService->update($subscription, [
                        'status' => SubscriptionStatus::ACTIVE,
                        'expires_at' => $plan->calcExpirationTime($subscription->expires_at)
                    ]);

                    SubscriptionRenewed::dispatch($subscription);
                    break;

                case PaymentStatus::REFUSED:
                case PaymentStatus::VOIDED:
                case PaymentStatus::EXPIRED:
                    $subscription = $this->subscriptionService->update($subscription, [
                        'status' => SubscriptionStatus::UNPAID
                    ]);

                    SubscriptionUnpaid::dispatch($subscription);
                    break;

                default:
                    // Valid also for AUTHORIZED and PENDING status
                    $subscription = $this->subscriptionService->update($subscription, [
                        'status' => SubscriptionStatus::PENDING,
                    ]);

                    SubscriptionPending::dispatch($subscription);
                    break;

                    // TODO handle REFUNDED payment: how the related subscription status should change? CANCELED?
            }
        }
    }
}
