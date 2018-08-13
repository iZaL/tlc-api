<?php

use App\Models\Load;
use Faker\Generator as Faker;

$factory->define(\App\Models\Load::class, function (Faker $faker) {
    $load = [
        'customer_id' => \App\Models\Customer::all()->count() > 0 ? \App\Models\Customer::all()->random()->first()->id : 1,
        'trailer_type_id' =>  \App\Models\TrailerType::all()->count() > 0 ? \App\Models\TrailerType::all()->random()->first()->id : 1,
        'origin_location_id' => 1,
        'destination_location_id' => 2,
        'fleet_count' => rand(1,4),
//        'distance' => rand(100,1000),
        'weight' => 100,
        'track_id' => strtoupper(str_random(8)),
        'request_documents' => $faker->boolean(50),
        'request_pictures' => $faker->boolean(50),
        'fixed_rate' => $faker->boolean(50),
        'load_date' => \Carbon\Carbon::now()->addDays(1,2)->toDateString(),
        'unload_date' => \Carbon\Carbon::now()->addDays(2,3)->toDateString(),
        'load_time_from' => '10:00:00',
        'load_time_to' => '12:00:00',
        'unload_time_from' => '12:00:00',
        'unload_time_to' => '16:00:00',
        'use_own_truck' => 0,
        'status' => Load::STATUS_PENDING,
        'receiver_name' => $faker->name,
        'receiver_email' => $faker->safeEmail,
        'receiver_mobile' => $faker->phoneNumber,
        'receiver_phone' => $faker->phoneNumber,
    ];
    return $load;
});

$factory->state(\App\Models\Load::class, 'approved', function ($faker) {
    return [
        'status' => Load::STATUS_APPROVED,
    ];
});

$factory->state(\App\Models\Load::class, 'pending', function ($faker) {
    return [
        'status' => Load::STATUS_PENDING,
    ];
});


