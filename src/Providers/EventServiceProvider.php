<?php

namespace EnricoNardo\EcommerceLayer\Providers;

use EnricoNardo\EcommerceLayer\Events\Order\OrderPlacing;
use EnricoNardo\EcommerceLayer\Listeners\SyncCustomerWithGateway;
use Illuminate\Support\ServiceProvider as ParentServiceProvider;

class EventServiceProvider extends ParentServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderPlacing::class => [
            SyncCustomerWithGateway::class,
        ],
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
