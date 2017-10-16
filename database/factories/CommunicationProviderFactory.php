<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\CommunicationProvider::class, function (Faker $faker) {
    return [
        'name_en' => $faker->name,
        'name_ar' => $faker->name,
        'name_hi' => $faker->name,
        'image' => $faker->imageUrl(100,100)
    ];
});
