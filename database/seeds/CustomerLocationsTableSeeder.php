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
        factory(\App\Models\CustomerLocation::class,1)->create(['latitude'=>48.8234055,'longitude' => 2.3072664,'type'=>'origin']);
        factory(\App\Models\CustomerLocation::class,1)->create(['latitude'=>43.296482,'longitude' =>  5.36978,'type' => 'destination']);
    }
}
