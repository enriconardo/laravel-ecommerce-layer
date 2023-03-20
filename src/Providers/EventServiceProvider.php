<?php

namespace EcommerceLayer\Providers;

use EcommerceLayer\Events\Order\OrderPlaced;
use EcommerceLayer\Listeners\SyncCustomerWithGateway;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ParentServiceProvider;

class EventServiceProvider extends ParentServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderPlaced::class => [
            SyncCustomerWithGateway::class,
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
