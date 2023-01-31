<?php

use Illuminate\Support\Facades\Route;

Route::prefix('ecommerce-layer/products')
    ->middleware('api')
    ->namespace('EnricoNardo\EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('/', 'ProductsController@list')->name('ecommerce-layer.products.list');

        Route::get('{id}', 'ProductsController@find')->name('ecommerce-layer.products.find');

        Route::post('/', 'ProductsController@create')->name('ecommerce-layer.products.create');

        Route::patch('{id}', 'ProductsController@update')->name('ecommerce-layer.products.update');

        Route::delete('{id}', 'ProductsController@delete')->name('ecommerce-layer.products.delete');

    });
