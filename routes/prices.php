<?php

use Illuminate\Support\Facades\Route;

Route::prefix('ecommerce-layer/prices')
    ->middleware('api')
    ->namespace('EnricoNardo\EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'PricesController@list')->name('ecommerce-layer.prices.list');

        Route::get('{id}', 'PricesController@find')->name('ecommerce-layer.prices.find');

        Route::post('/', 'PricesController@create')->name('ecommerce-layer.prices.create');

        Route::patch('{id}', 'PricesController@update')->name('ecommerce-layer.prices.update');

        Route::delete('{id}', 'PricesController@delete')->name('ecommerce-layer.prices.delete');

    });
