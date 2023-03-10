<?php

use Illuminate\Support\Facades\Route;

$middlewares = config('ecommerce-layer.http.middlewares');

Route::prefix('ecommerce-layer/customers')
    ->middleware($middlewares)
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'CustomerController@list')->name('ecommerce-layer.customers.list');

        Route::get('{id}', 'CustomerController@find')->name('ecommerce-layer.customers.find');

        Route::post('/', 'CustomerController@create')->name('ecommerce-layer.customers.create');

        Route::patch('{id}', 'CustomerController@update')->name('ecommerce-layer.customers.update');

        Route::delete('{id}', 'CustomerController@delete')->name('ecommerce-layer.customers.delete');

    });
