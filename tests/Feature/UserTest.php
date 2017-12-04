<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    public function test_get_correct_user_types()
    {
        $admin = factory(User::class)->create(['admin'=>1]);
        $this->assertEquals($admin->user_type,'admin');

        $shipper = factory(User::class)->create();
        $shipper->shipper()->create();
        $this->assertEquals($shipper->user_type,'shipper');
        $this->assertNotEquals($shipper->user_type,'driver');
        $this->assertNotEquals($shipper->user_type,'admin');
        $this->assertNotEquals($shipper->user_type,'default');

        $driver = factory(User::class)->create();
        $driver->driver()->create();
        $this->assertEquals($driver->user_type,'driver');


        $default = factory(User::class)->create();
        $this->assertEquals($default->user_type,'default');

    }


}
