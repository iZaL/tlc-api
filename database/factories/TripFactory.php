<?php

use App\Models\Driver;
use App\Models\Load;
use App\Models\Trip;
use Faker\Generator as Faker;

$factory->define(\App\Models\Trip::class, function (Faker $faker) {
    return [
        'load_id'    => \App\Models\Load::all()->count() ? \App\Models\Load::all()->random()->first()->id : 1,
        'driver_id'  => \App\Models\Driver::all()->count() ? \App\Models\Driver::all()->random()->first()->id : 1,
        'amount'     => rand(100, 900),
        'started_at' => \Carbon\Carbon::now()->subDays(rand(1, 10))->toDateTimeString(),
        'ended_at' => \Carbon\Carbon::now()->subDays(rand(1, 10))->toDateTimeString(),
        'status' => Trip::STATUS_PENDING
    ];
});

$factory->define(\App\Models\TripDocumentation::class, function (Faker $faker) {
    return [
        'load_id'   => \App\Models\Load::all()->random()->first()->id,
        'driver_id' => \App\Models\Driver::all()->random()->first()->id,
        'amount'    => rand(10, 100),
        'type'      => 'POD',
        'file_path' => $faker->imageUrl(500, 500)
    ];
});
