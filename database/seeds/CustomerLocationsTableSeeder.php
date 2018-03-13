<?php

use Illuminate\Database\Seeder;

class CustomerLocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\CustomerLocation::class,5)->create();
    }
}
