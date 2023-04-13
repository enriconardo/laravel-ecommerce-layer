<?php

namespace EcommerceLayer\Jobs;

use Carbon\Carbon;
use EcommerceLayer\Enums\SubscriptionStatus;
use EcommerceLayer\Models\Subscription;
use EcommerceLayer\Services\SubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
    public function handle(SubscriptionService $subscriptionService): void {
        $now = Carbon::now();

        // Search for expired subscriptions
        $expiredSubs = Subscription::whereDate('expires_at', '<=', $now)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->get();

        // Foreach subscriptions, generate try to renew it
        foreach ($expiredSubs as $sub) {
            $subscriptionService->renew($sub);
        }
    }
}
