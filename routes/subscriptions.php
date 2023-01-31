<?php

use Illuminate\Support\Facades\Route;

Route::prefix('ecommerce-layer/subscriptions')
    ->middleware('api')
    ->namespace('EnricoNardo\EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'SubscriptionsController@list')->name('ecommerce-layer.subscriptions.list');

        Route::get('{id}', 'SubscriptionsController@find')->name('ecommerce-layer.subscriptions.find');

        Route::post('/', 'SubscriptionsController@create')->name('ecommerce-layer.subscriptions.create');

        Route::patch('{id}', 'SubscriptionsController@update')->name('ecommerce-layer.subscriptions.update');

        Route::delete('{id}', 'SubscriptionsController@delete')->name('ecommerce-layer.subscriptions.delete');

    });
