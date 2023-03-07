<?php

use Illuminate\Support\Facades\Route;

$middlewares = config('ecommerce-layer.http.middlewares');

Route::prefix('ecommerce-layer/subscriptions')
    ->middleware($middlewares)
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'SubscriptionController@list')->name('ecommerce-layer.subscriptions.list');

        Route::get('{id}', 'SubscriptionController@find')->name('ecommerce-layer.subscriptions.find');

        Route::post('/', 'SubscriptionController@create')->name('ecommerce-layer.subscriptions.create');

        Route::patch('{id}', 'SubscriptionController@update')->name('ecommerce-layer.subscriptions.update');

        Route::delete('{id}', 'SubscriptionController@delete')->name('ecommerce-layer.subscriptions.delete');

    });
