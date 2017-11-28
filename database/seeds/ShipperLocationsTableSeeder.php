<?php

use Illuminate\Database\Seeder;

class ShipperLocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Location::class,5)->create();
    }
}
