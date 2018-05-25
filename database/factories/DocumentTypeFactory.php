<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\DocumentType::class, function (Faker $faker) {
    return [
        'name_en' => $faker->word,
        'name_ar' => $faker->word,
        'name_hi' => $faker->word,
        'description_en' => $faker->word,
        'description_ar' => $faker->word,
        'description_hi' => $faker->word,
        'image' => $faker->imageUrl(100,100)
    ];
});
