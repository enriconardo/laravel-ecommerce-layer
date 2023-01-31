<?php

use Illuminate\Support\Facades\Route;

Route::prefix('ecommerce-layer/orders')
    ->middleware('api')
    ->namespace('EnricoNardo\EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('{orderId}/line-items', 'LineItemsController@list')->name('ecommerce-layer.line-items.list');

        Route::get('{orderId}/line-items/{id}', 'LineItemsController@find')->name('ecommerce-layer.line-items.find');

        Route::post('{orderId}/line-items', 'LineItemsController@create')->name('ecommerce-layer.line-items.create');

        Route::patch('{orderId}/line-items/{id}', 'LineItemsController@update')->name('ecommerce-layer.line-items.update');

        Route::delete('{orderId}/line-items/{id}', 'LineItemsController@delete')->name('ecommerce-layer.line-items.delete');

    });
