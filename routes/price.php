<?php

use Illuminate\Support\Facades\Route;

Route::prefix('ecommerce-layer/prices')
    ->middleware('api')
    ->namespace('EnricoNardo\EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'PriceController@list')->name('ecommerce-layer.prices.list');

        Route::get('{id}', 'PriceController@find')->name('ecommerce-layer.prices.find');

        Route::post('/', 'PriceController@create')->name('ecommerce-layer.prices.create');

        Route::patch('{id}', 'PriceController@update')->name('ecommerce-layer.prices.update');

        Route::delete('{id}', 'PriceController@delete')->name('ecommerce-layer.prices.delete');

    });