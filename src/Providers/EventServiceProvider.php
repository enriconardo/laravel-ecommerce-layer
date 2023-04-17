<?php

namespace EcommerceLayer\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ParentServiceProvider;

class EventServiceProvider extends ParentServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \EcommerceLayer\Events\Order\OrderPlaced::class => [
            \EcommerceLayer\Listeners\SyncCustomerWithGateway::class,
            \EcommerceLayer\Listeners\CallWebhooks::class,
        ],
        \EcommerceLayer\Events\Order\OrderCompleted::class => [
            \EcommerceLayer\Listeners\CallWebhooks::class,
        ],
        \EcommerceLayer\Events\Order\OrderCanceled::class => [
            \EcommerceLayer\Listeners\CallWebhooks::class,
        ],
        \EcommerceLayer\Events\Subscriptions\SubscriptionActivated::class => [
            \EcommerceLayer\Listeners\CallWebhooks::class,
        ],
        \EcommerceLayer\Events\Subscriptions\SubscriptionCanceled::class => [
            \EcommerceLayer\Listeners\CallWebhooks::class,
        ],
        \EcommerceLayer\Events\Subscriptions\SubscriptionPending::class => [
            \EcommerceLayer\Listeners\CallWebhooks::class,
        ],
        \EcommerceLayer\Events\Subscriptions\SubscriptionRenewed::class => [
            \EcommerceLayer\Listeners\CallWebhooks::class,
        ],
        \EcommerceLayer\Events\Subscriptions\SubscriptionUnpaid::class => [
            \EcommerceLayer\Listeners\CallWebhooks::class,
        ],
        \EcommerceLayer\Events\Payment\PaymentUpdated::class => [
            \EcommerceLayer\Listeners\HandleSubscription::class
        ],
        \EcommerceLayer\Gateways\Events\GatewayPaymentUpdated::class => [
            \EcommerceLayer\Listeners\Handle3DSecurePayment::class
        ],
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        parent::register();
    }
}
