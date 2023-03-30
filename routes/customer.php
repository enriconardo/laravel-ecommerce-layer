<?php

use Illuminate\Support\Facades\Route;

$middlewares = config('ecommerce-layer.http.routes.middlewares');
$prefix = config('ecommerce-layer.http.routes.prefix');

Route::prefix($prefix)
    ->middleware($middlewares)
    ->namespace('EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('customers', 'CustomerController@list')->name('ecommerce-layer.customers.list');

        Route::get('customers/{id}', 'CustomerController@find')->name('ecommerce-layer.customers.find');

        Route::post('customers', 'CustomerController@create')->name('ecommerce-layer.customers.create');

        Route::put('customers/{id}', 'CustomerController@createOrUpdate')->name('ecommerce-layer.customers.createOrUpdate');

        Route::patch('customers/{id}', 'CustomerController@update')->name('ecommerce-layer.customers.update');

        Route::delete('customers/{id}', 'CustomerController@delete')->name('ecommerce-layer.customers.delete');

    });
