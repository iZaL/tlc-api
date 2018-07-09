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
        factory(\App\Models\TruckModel::class,10)->create();

        factory(\App\Models\TrailerMake::class,2)->create();
        factory(\App\Models\TrailerType::class)->create(['name_en'=>'Flatbed']);
        factory(\App\Models\TrailerType::class)->create(['name_en'=>'Reefer']);
        factory(\App\Models\TrailerType::class)->create(['name_en'=>'Lowbed']);
        factory(\App\Models\TrailerType::class)->create(['name_en'=>'Utility']);

        $trailer = factory(\App\Models\Trailer::class)->create();
        $truck = factory(\App\Models\Truck::class)->create(['trailer_id'=>$trailer->id]);

    }
}
