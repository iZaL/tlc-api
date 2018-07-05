<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\TrailerMake::class, function (Faker $faker) {
    return [
        'name_en' => $faker->name,
        'name_ar' => $faker->name,
        'name_hi' => $faker->name,
    ];
});


$factory->define(\App\Models\TrailerType::class, function (Faker $faker) {
    return [
//        'make_id'    => \App\Models\TrailerMake::all()->count() > 0 ? \App\Models\TrailerMake::all()->random()->first()->id : 1,
        'name_en' => $faker->name,
        'name_ar' => $faker->name,
        'name_hi' => $faker->name,
    ];
});


$factory->define(\App\Models\Trailer::class, function (Faker $faker) {
    return [
        'make_id'    => \App\Models\TrailerMake::all()->count() > 0 ? \App\Models\TrailerMake::all()->random()->first()->id : 1,
        'type_id'    => \App\Models\TrailerType::all()->count() > 0 ? \App\Models\TrailerType::all()->random()->first()->id : 1,
//        'truck_id'          => \App\Models\Truck::first() ? \App\Models\Truck::all()->random()->first()->id : 1,
        'max_weight' => rand(10000, 90000),
        'year'       => rand(1900, 2016),
        'length'     => rand(100, 200),
        'width'      => rand(100, 200),
        'height'     => rand(100, 200),
        'image'      => $faker->imageUrl(500, 500),
    ];
});
