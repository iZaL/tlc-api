<?php

use Illuminate\Database\Seeder;

class SecurityPassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\SecurityPass::class,5)->create();
    }
}
