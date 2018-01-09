<?php


Route::get('/', function () {

    $date = \Carbon\Carbon::parse('now');
    dd($date);
});
