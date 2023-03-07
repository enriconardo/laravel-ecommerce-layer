<?php

use Illuminate\Support\Facades\Route;

$middlewares = config('ecommerce-layer.http.middlewares');

Route::prefix('ecommerce-layer/orders')
    ->middleware($middlewares)
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'OrderController@list')->name('ecommerce-layer.orders.list');

        Route::get('{id}', 'OrderController@find')->name('ecommerce-layer.orders.find');

        Route::post('/', 'OrderController@create')->name('ecommerce-layer.orders.create');

        Route::patch('{id}', 'OrderController@update')->name('ecommerce-layer.orders.update');

        Route::delete('{id}', 'OrderController@delete')->name('ecommerce-layer.orders.delete');

        Route::post('{id}/place', 'OrderController@place')->name('ecommerce-layer.orders.place');

    });
