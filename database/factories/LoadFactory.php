<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Load::class, function (Faker $faker) {
    return [
        'shipper_id' => \App\Models\Shipper::all()->count() > 0 ? \App\Models\Shipper::all()->random()->first()->id : 1,
        'trailer_id' =>  \App\Models\Trailer::all()->count() > 0 ? \App\Models\Trailer::all()->random()->first()->id : 1,
        'origin_location_id' => \App\Models\ShipperLocation::all()->count() > 0 ? \App\Models\ShipperLocation::all()->random()->first()->id : 1,
        'destination_location_id' => \App\Models\ShipperLocation::all()->count() > 0 ? \App\Models\ShipperLocation::all()->random()->first()->id : 1,
        'fleet_count' => rand(1,4),
        'price' => rand(100,1000),
        'distance' => rand(100,1000),
        'invoice_id' => str_random(10),
        'request_documents' => $faker->boolean(50),
        'request_pictures' => $faker->boolean(50),
        'fixed_rate' => $faker->boolean(50),
        'status' => 'busy',
        'load_date' => \Carbon\Carbon::now()->addDays(1,10)->toDateString(),
        'load_time' => '2pm - 5pm',
        'unload_date' => \Carbon\Carbon::now()->addDays(1,10)->toDateString(),
        'unload_time' => '1pm - 10pm',
        'use_own_truck' => 0
    ];
});

$factory->state(\App\Models\Load::class, 'waiting', function ($faker) {
    return [
        'status' => 'waiting',
    ];
});

$factory->state(\App\Models\Load::class, 'pending', function ($faker) {
    return [
        'status' => 'pending',
    ];
});

$factory->define(\App\Models\JobDocumentation::class, function (Faker $faker) {
    return [
        'load_id' => \App\Models\Shipper::all()->random()->first()->id,
        'driver_id' => \App\Models\Driver::all()->random()->first()->id,
        'amount' => rand(10,100),
        'type' => 'POD',
        'file_path' => $faker->imageUrl(500,500)
    ];
});

$factory->define(\App\Models\Job::class, function (Faker $faker) {
    return [
        'load_id' =>  \App\Models\Shipper::all()->random()->first()->id,
        'driver_id' =>  \App\Models\Driver::all()->random()->first()->id,
        'amount' => rand(10,100),
        'reached_at' => \Carbon\Carbon::now()->addDays(rand(1,10))->toDateTimeString()
    ];
});


