<?php

use Illuminate\Database\Seeder;

class PassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Pass::class,5)->create();
    }
}
