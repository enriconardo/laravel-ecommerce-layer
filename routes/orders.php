<?php

use Illuminate\Support\Facades\Route;

Route::prefix('ecommerce-layer/orders')
    ->middleware('api')
    ->namespace('EnricoNardo\EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'OrdersController@list')->name('ecommerce-layer.orders.list');

        Route::get('{id}', 'OrdersController@find')->name('ecommerce-layer.orders.find');

        Route::post('/', 'OrdersController@create')->name('ecommerce-layer.orders.create');

        Route::patch('{id}', 'OrdersController@update')->name('ecommerce-layer.orders.update');

        Route::delete('{id}', 'OrdersController@delete')->name('ecommerce-layer.orders.delete');

        Route::post('{id}/place', 'OrdersController@place')->name('ecommerce-layer.orders.place');

    });
