<?php

use Illuminate\Database\Seeder;

class PackagingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Packaging::class)->create(['name_en'=>'Pallet']);
        factory(\App\Models\Packaging::class)->create(['name_en'=>'Loose']);
        factory(\App\Models\Packaging::class)->create(['name_en'=>'Bulk']);
    }
}
