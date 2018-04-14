<?php

use Illuminate\Database\Seeder;

class TrailersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\TrailerMake::class,2)->create();
        factory(\App\Models\TrailerType::class)->create(['name_en'=>'Flatbed']);
        factory(\App\Models\TrailerType::class)->create(['name_en'=>'Reefer']);
        factory(\App\Models\TrailerType::class)->create(['name_en'=>'Lowbed']);
        factory(\App\Models\TrailerType::class)->create(['name_en'=>'Utility']);
//        factory(\App\Models\Trailer::class)->create(['name_en'=>'Flatbed']);
//        factory(\App\Models\Trailer::class)->create(['name_en'=>'Reefer']);
//        factory(\App\Models\Trailer::class)->create(['name_en'=>'Lowbed']);


//        $driverUser = \App\Models\User::where('email','driver@test.com')->first();

    }
}
