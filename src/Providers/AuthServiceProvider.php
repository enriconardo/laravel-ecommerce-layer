<?php

namespace EnricoNardo\EcommerceLayer\Providers;

use Illuminate\Support\ServiceProvider as ParentServiceProvider;

class AuthServiceProvider extends ParentServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \EnricoNardo\EcommerceLayer\Models\Order::class => \EnricoNardo\EcommerceLayer\Policies\OrderPolicy::class,
        \EnricoNardo\EcommerceLayer\Models\LineItem::class => \EnricoNardo\EcommerceLayer\Policies\LineItemPolicy::class
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
