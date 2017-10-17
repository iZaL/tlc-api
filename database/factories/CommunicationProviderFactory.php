<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\CommunicationProvider::class, function (Faker $faker) {
    return [
        'name_en' => $faker->word,
        'name_ar' => $faker->word,
        'name_hi' => $faker->word,
        'image' => $faker->imageUrl(100,100)
    ];
});
