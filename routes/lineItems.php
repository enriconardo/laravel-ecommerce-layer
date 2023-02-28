<?php

use Illuminate\Support\Facades\Route;

Route::prefix('ecommerce-layer')
    ->middleware('api')
    ->namespace('EnricoNardo\EcommerceLayer\Http\Controllers')
    ->group(function () {

        Route::get('line-items', 'LineItemsController@list')->name('ecommerce-layer.line-items.list');

        Route::get('line-items/{id}', 'LineItemsController@find')->name('ecommerce-layer.line-items.find');

        Route::post('line-items', 'LineItemsController@create')->name('ecommerce-layer.line-items.create');

        Route::patch('line-items/{id}', 'LineItemsController@update')->name('ecommerce-layer.line-items.update');

        Route::delete('line-items/{id}', 'LineItemsController@delete')->name('ecommerce-layer.line-items.delete');

    });
