<?php

Route::get('/', function () {
    return view('welcome');
});

Route::get('test','TestController@index');

Route::get('loads/{id}/drivers','Api\Customer\LoadDriversController@getDriversForLoad');
