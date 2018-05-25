<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\TripDocument::class, function (Faker $faker) {
    return [
        'document_type_id' => 1,
        'trip_id' => 1,
        'url' => $faker->imageUrl(370,790),
        'extension' => 'image'
    ];
});
