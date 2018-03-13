<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Load::class, function (Faker $faker) {
    $load = [
        'customer_id' => \App\Models\Customer::all()->count() > 0 ? \App\Models\Customer::all()->random()->first()->id : 1,
        'trailer_id' =>  \App\Models\Trailer::all()->count() > 0 ? \App\Models\Trailer::all()->random()->first()->id : 1,
        'origin_location_id' => \App\Models\CustomerLocation::all()->count() > 0 ? \App\Models\CustomerLocation::all()->random()->first()->id : 1,
        'destination_location_id' => \App\Models\CustomerLocation::all()->count() > 0 ? \App\Models\CustomerLocation::all()->random()->first()->id : 1,
        'fleet_count' => rand(1,4),
        'price' => rand(100,1000),
        'distance' => rand(100,1000),
        'invoice_id' => str_random(10),
        'request_documents' => $faker->boolean(50),
        'request_pictures' => $faker->boolean(50),
        'fixed_rate' => $faker->boolean(50),
        'load_date' => \Carbon\Carbon::now()->addDays(1,10)->toDateString(),
        'load_time' => '2pm - 5pm',
        'unload_date' => \Carbon\Carbon::now()->addDays(1,10)->toDateString(),
        'unload_time' => '1pm - 10pm',
        'use_own_truck' => 0,
        'status' => 'pending',
    ];
    return $load;
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


