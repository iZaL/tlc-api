<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\TruckMake::class, function (Faker $faker) {
    return [
        'name_en' => $faker->name,
        'name_ar' => $faker->name,
        'name_hi' => $faker->name,
        'image' => $faker->imageUrl(500, 500),
    ];
});

$factory->define(\App\Models\TruckModel::class, function (Faker $faker) {
    return [
        'name_en' => $faker->name,
        'name_ar' => $faker->name,
        'name_hi' => $faker->name,
        'image' => $faker->imageUrl(500, 500),
    ];
});

$factory->define(\App\Models\Truck::class, function (Faker $faker) {
    return [
        'make_id'             => \App\Models\TruckMake::all()->random()->first()->id,
        'model_id'            => \App\Models\TruckModel::all()->random()->first()->id,
        'trailer_id'          => \App\Models\Trailer::all()->random()->first()->id,
//        'shipper_id'          => \App\Models\Shipper::all()->random()->first()->id,
//        'driver_id'           => \App\Models\Driver::all()->random()->first()->id,
//        'country_id'          => \App\Models\Country::all()->random()->first()->id,
        'plate_number'        => rand(10000, 90000),
        'registration_number' => str_random(8),
        'registration_expiry' => \Carbon\Carbon::now()->addYear(rand(1, 5))->addDays(rand(1, 365))->toDateString(),
        'max_weight'          => rand(10000, 90000),
        'year'                => rand(1900, 2016),
        'image'               => $faker->imageUrl(500, 500),
    ];
});
