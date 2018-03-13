<?php


Route::get('/', function () {
});

Route::get('loads/{id}/drivers','Api\Shipper\LoadDriversController@getDriversForLoad');
