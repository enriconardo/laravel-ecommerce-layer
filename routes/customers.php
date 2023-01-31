<?php

use Illuminate\Support\Facades\Route;

Route::prefix('ecommerce-layer/customers')
    ->middleware('api')
    ->namespace('EnricoNardo\EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'CustomersController@list')->name('ecommerce-layer.customers.list');

        Route::get('{id}', 'CustomersController@find')->name('ecommerce-layer.customers.find');

        Route::post('/', 'CustomersController@create')->name('ecommerce-layer.customers.create');

        Route::patch('{id}', 'CustomersController@update')->name('ecommerce-layer.customers.update');

        Route::delete('{id}', 'CustomersController@delete')->name('ecommerce-layer.customers.delete');

    });
