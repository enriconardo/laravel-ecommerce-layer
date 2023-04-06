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
