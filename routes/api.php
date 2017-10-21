<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api'], function () {

    Route::middleware(['auth:api'])->group(function () {

        Route::post('loads', 'LoadsController@bookLoad');
        Route::get('loads', 'LoadsController@getLoads');

    });

});

