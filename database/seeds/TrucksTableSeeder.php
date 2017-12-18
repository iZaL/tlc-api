<?php

use Illuminate\Database\Seeder;

class TrucksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\TruckMake::class,5)->create();
        factory(\App\Models\TruckModel::class,50)->create();
        factory(\App\Models\Truck::class,5)->create();
    }
}
