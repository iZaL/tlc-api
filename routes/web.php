<?php


Route::get('/', function () {

    $driver =\App\Models\Driver::first();

    if($driver->truck) {
        $driver->truck()->save(new \App\Models\TruckModel(['make_id'=>1,'model_id'=>2]));

//        $driver->truck()->update(['make_id'=>3]);
    } else {
        $driver->truck()->save(new \App\Models\TruckModel(['make_id'=>1,'model_id'=>2]));
    }

});
