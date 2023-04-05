<?php

namespace EcommerceLayer\Jobs;

use Carbon\Carbon;
use EcommerceLayer\Enums\OrderStatus;
use EcommerceLayer\Enums\SubscriptionStatus;
use EcommerceLayer\Models\Order;
use EcommerceLayer\Models\Subscription;
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
        OrderService $orderService
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
                'status' => OrderStatus::OPEN,
                'gateway_id' => $sourceOrder->gateway_id,
                'customer_id' => $sourceOrder->customer_id,
                'currency' => $sourceOrder->currency
            ]);

            // Set subscription status as pending while the renewal order is waiting for payment.
            // Also update the source order.
            $subscriptionService->update($sub, [
                'status' => SubscriptionStatus::PENDING,
                'source_order_id' => $newOrder->id,
            ]);

            // Pay the order
            $newOrder->pay();
        }

        // Cercare le subs scadute in stato attivo e tentare un nuovo pagamento, status = PENDING o WAITING_FOR_RENEW
        // Se pagamento OK allora aggiorno expires_at e status = ACTIVE
        // Se pagamento NON ok allora status = UNPAID
    }
}
