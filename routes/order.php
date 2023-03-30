<?php

use Illuminate\Support\Facades\Route;

$middlewares = config('ecommerce-layer.http.routes.middlewares');
$prefix = config('ecommerce-layer.http.routes.prefix');

Route::prefix($prefix)
    ->middleware($middlewares)
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('orders', 'OrderController@list')->name('ecommerce-layer.orders.list');

        Route::get('orders/{id}', 'OrderController@find')->name('ecommerce-layer.orders.find');

        Route::post('orders', 'OrderController@create')->name('ecommerce-layer.orders.create');

        Route::patch('orders/{id}', 'OrderController@update')->name('ecommerce-layer.orders.update');

        Route::delete('orders/{id}', 'OrderController@delete')->name('ecommerce-layer.orders.delete');

        Route::post('orders/{id}/place', 'OrderController@place')->name('ecommerce-layer.orders.place');

    });
