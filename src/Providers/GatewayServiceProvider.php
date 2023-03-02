<?php

namespace EcommerceLayer\Providers;

use EcommerceLayer\Gateways\GatewayProviderFactory;
use EcommerceLayer\Gateways\Stripe\Stripe;
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
        $this->app->singleton(GatewayProviderFactory::class, function () {
            return new GatewayProviderFactory();
        });

        // Enable the gateways
        $this->app->make(GatewayProviderFactory::class)->enableGateway(new Stripe);
    }
}
