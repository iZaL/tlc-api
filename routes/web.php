<?php


Route::get('/', function () {
});

Route::get('test','TestController@index');

Route::get('loads/{id}/drivers','Api\Customer\LoadDriversController@getDriversForLoad');
