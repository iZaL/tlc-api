<?php

use Illuminate\Database\Seeder;

class ShippersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Shipper::class,5)->create();
        factory(\App\Models\Employee::class,5)->create();
    }
}
