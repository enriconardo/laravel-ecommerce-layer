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
        $this->loadRoutesFrom(__DIR__.'/../../routes/customer.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/lineItem.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/order.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/price.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/product.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/subscription.php');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
