<?php

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('test','TestController@index');

Route::get('loads/{id}/drivers','Api\Customer\LoadDriversController@getDriversForLoad');

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
