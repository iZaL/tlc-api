<?php

use Illuminate\Database\Seeder;

class DocumentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(\App\Models\DocumentType::class)->create(['name_en'=>'Cargo Receipt']);
        factory(\App\Models\DocumentType::class)->create(['name_en'=>'Proof of Delivery']);
//        factory(\App\Models\DocumentType::class)->create(['name_en'=>'Proof of Delivery']);

    }
}
