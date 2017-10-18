<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Load::class, function (Faker $faker) {
    return [
        'shipper_id' => \App\Models\Shipper::all()->random()->first()->id,
        'trailer_id' => \App\Models\Trailer::all()->random()->first()->id,
        'origin_location_id' => \App\Models\Location::all()->random()->first()->id,
        'destination_location_id' => \App\Models\Location::all()->random()->first()->id,
        'price' => rand(100,1000),
        'distance' => rand(100,1000),
        'invoice_id' => str_random(10),
        'request_documents' => $faker->boolean(50),
        'request_pictures' => $faker->boolean(50),
        'fixed_rate' => $faker->boolean(50),
        'status' => 'busy',
        'scheduled_at' => \Carbon\Carbon::now()->addDays(1,10)->toDateTimeString()
    ];
});

$factory->define(\App\Models\LoadDocument::class, function (Faker $faker) {
    return [
        'load_id' => \App\Models\Shipper::all()->random()->first()->id,
        'truck_id' => \App\Models\Truck::all()->random()->first()->id,
        'amount' => rand(10,100),
        'type' => 'POD',
        'file_path' => $faker->imageUrl(500,500)
    ];
});

$factory->define(\App\Models\LoadTruck::class, function (Faker $faker) {
    return [
        'load_id' =>  \App\Models\Shipper::all()->random()->first()->id,
        'truck_id' =>  \App\Models\Shipper::all()->random()->first()->id,
        'amount' => rand(10,100),
        'reached_at' => \Carbon\Carbon::now()->addDays(rand(1,10))->toDateTimeString()
    ];
});


