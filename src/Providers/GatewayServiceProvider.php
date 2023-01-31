<?php

namespace EnricoNardo\EcommerceLayer\Providers;

use EnricoNardo\EcommerceLayer\Gateways\GatewayServiceFactory;
use EnricoNardo\EcommerceLayer\Gateways\Stripe\Stripe;
use Illuminate\Support\ServiceProvider as ParentServiceProvider;

class GatewayServiceProvider extends ParentServiceProvider
{
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
        // Register the singleton GatewayFactory
        $this->app->singleton(GatewayServiceFactory::class, function () {
            return new GatewayServiceFactory();
        });

        // Enable the gateways
        $this->app->make(GatewayServiceFactory::class)->enableGateway(new Stripe);
    }
}
