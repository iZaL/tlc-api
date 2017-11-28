<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\LoadDriver::class, function (Faker $faker) {
    return [
        'load_id' => \App\Models\Load::all()->random()->first()->id,
        'driver_id' => \App\Models\Driver::all()->random()->first()->id,
        'amount' => rand(100,900),
        'reached_at' => \Carbon\Carbon::now()->subDays(rand(1,10))->toDateTimeString()
    ];
});
