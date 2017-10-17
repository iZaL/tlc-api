<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Language::class, function (Faker $faker) {
    return [
        'name_en' => $faker->word,
        'name_ar' => $faker->word,
        'name_hi' => $faker->word,
        'code' => $faker->languageCode,
    ];
});
