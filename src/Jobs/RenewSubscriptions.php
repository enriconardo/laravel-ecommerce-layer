<?php

namespace EcommerceLayer\Jobs;

use Carbon\Carbon;
use EcommerceLayer\Enums\OrderStatus;
use EcommerceLayer\Enums\PaymentStatus;
use EcommerceLayer\Enums\SubscriptionStatus;
use EcommerceLayer\Models\Subscription;
use EcommerceLayer\Services\LineItemService;
use EcommerceLayer\Services\OrderService;
use EcommerceLayer\Services\SubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RenewSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
        SubscriptionService $subscriptionService,
        OrderService $orderService,
        LineItemService $lineItemService,
    ): void {
        $now = Carbon::now();

        // Search for expired subscriptions
        $expiredSubs = Subscription::whereDate('expires_at', '<=', $now)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->get();

        // Foreach subscriptions, generate a new order and pay it
        foreach ($expiredSubs as $sub) {
            /** @var Subscription $sub */
            $sourceOrder = $sub->source_order;

            $newOrder = $orderService->create([
                'gateway_id' => $sourceOrder->gateway_id,
                'customer_id' => $sourceOrder->customer_id,
                'currency' => $sourceOrder->currency,
                'billing_address' => $sourceOrder->billing_address,
                'payment_method' => $sourceOrder->payment_method,
                'payment_status' => PaymentStatus::UNPAID
            ]);

            $lineItemService->create([
                'price_id' => $sub->price_id,
                'quantity' => 1
            ], $newOrder);

            $newOrder = $orderService->update($newOrder, [
                'status' => OrderStatus::OPEN,
            ]);

            // Set subscription status as pending while the renewal order is waiting for payment.
            // Also update the source order.
            $subscriptionService->update($sub, [
                'status' => SubscriptionStatus::PENDING,
                'source_order_id' => $newOrder->id,
            ]);

            // Pay the order
            $newOrder = $orderService->pay($newOrder);
        }
    }
}
