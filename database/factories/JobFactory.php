<?php

use App\Models\Driver;
use App\Models\Load;
use Faker\Generator as Faker;

$factory->define(\App\Models\Job::class, function (Faker $faker) {
    return [
        'load_id' =>  \App\Models\Load::all()->count()  ? \App\Models\Load::all()->random()->first()->id : 1,
        'driver_id' => \App\Models\Driver::all()->count() ? \App\Models\Driver::all()->random()->first()->id : 1,
        'amount' => rand(100,900),
        'reached_at' => \Carbon\Carbon::now()->subDays(rand(1,10))->toDateTimeString(),
        'status' => 'pending'
    ];
});
