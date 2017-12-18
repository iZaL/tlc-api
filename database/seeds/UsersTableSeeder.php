<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = factory(User::class)->create(['email' => 'admin@test.com', 'admin' => 1]);
        $driver1 = factory(\App\Models\User::class)->create(['email' => 'driver@test.com', 'password' => bcrypt('password'), 'active' => 1]);
        $shipper1 = factory(\App\Models\User::class)->create(['email' => 'shipper@test.com', 'password' => bcrypt('password'), 'active' => 1]);

        $driver1->driver()->create(['active'=>1]);
        $shipper1->shipper()->create(['active'=>1]);

    }
}
