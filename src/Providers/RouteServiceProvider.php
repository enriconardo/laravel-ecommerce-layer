<?php

namespace EnricoNardo\EcommerceLayer\Providers;


use Illuminate\Support\ServiceProvider as ParentServiceProvider;

class RouteServiceProvider extends ParentServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/customers.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/lineItems.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/orders.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/prices.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/products.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/subscriptions.php');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
