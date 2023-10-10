<?php

use Illuminate\Support\Facades\Route;

$middlewares = config('ecommerce-layer.http.routes.middlewares');
$prefix = config('ecommerce-layer.http.routes.prefix');

Route::prefix($prefix)
    ->middleware($middlewares)
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('subscriptions', 'SubscriptionController@list')->name('ecommerce-layer.subscriptions.list');

        Route::get('subscriptions/{id}', 'SubscriptionController@find')->name('ecommerce-layer.subscriptions.find');

        // Route::post('subscriptions', 'SubscriptionController@create')->name('ecommerce-layer.subscriptions.create');

        Route::patch('subscriptions/{id}', 'SubscriptionController@update')->name('ecommerce-layer.subscriptions.update');

        // Route::delete('subscriptions/{id}', 'SubscriptionController@delete')->name('ecommerce-layer.subscriptions.delete');

        Route::post('subscriptions/{id}/cancel', 'SubscriptionController@cancel')->name('ecommerce-layer.subscriptions.cancel');

    });
