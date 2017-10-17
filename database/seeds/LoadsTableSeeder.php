<?php

use Illuminate\Database\Seeder;

class LoadsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Load::class,5)->create();
        factory(\App\Models\LoadTruck::class,5)->create();
    }
}
