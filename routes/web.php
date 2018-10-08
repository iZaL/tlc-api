<?php

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('test','TestController@index');

Route::get('loads/{id}/drivers','Api\Customer\LoadDriversController@getDriversForLoad');

Auth::routes();


//Route::resource('customers','C');
Route::group(['namespace' => 'Admin',  'middleware' => ['auth']], function () {
    Route::resource('customers','CustomersController');
});


Route::get('/', 'HomeController@index')->name('home');
