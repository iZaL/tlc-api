<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Country::class, 1)->create(['name_en'=>'Kuwait']);
//        factory(\App\Models\Country::class, 1)->create(['name_en'=>'Iraq']);
//        factory(\App\Models\Country::class, 1)->create(['name_en'=>'India']);
        factory(\App\Models\Country::class,5)->create();
    }
}
