<?php

use Illuminate\Support\Facades\Route;

$guard = config('ecommerce-layer.auth.guard');

Route::prefix('ecommerce-layer/customers')
    ->middleware(["api", "auth:$guard"])
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'CustomerController@list')->name('ecommerce-layer.customers.list');

        Route::get('{id}', 'CustomerController@find')->name('ecommerce-layer.customers.find');

        Route::post('/', 'CustomerController@create')->name('ecommerce-layer.customers.create');

        Route::patch('{id}', 'CustomerController@update')->name('ecommerce-layer.customers.update');

        Route::delete('{id}', 'CustomerController@delete')->name('ecommerce-layer.customers.delete');

    });
