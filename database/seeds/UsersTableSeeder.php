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
        $admin = factory(User::class)->create(['email' => 'admin@test.com', 'admin' => 1,'api_token'=>'admin']);
        $driverUser = factory(\App\Models\User::class)->create(['email' => 'driver@test.com', 'password' => bcrypt('password'), 'active' => 1,'api_token'=>'driver']);
        $customer1 = factory(\App\Models\User::class)->create(['email' => 'customer@test.com', 'password' => bcrypt('password'), 'active' => 1,'api_token'=>'customer']);

        factory(\App\Models\Driver::class)->create(['user_id'=>$driverUser->id]);
        factory(\App\Models\Customer::class)->create(['user_id'=>$customer1->id]);
//        $driver1->driver()->create(['active'=>1]);
//        $customer1->customer()->create(['active'=>1]);

    }
}
