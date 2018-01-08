<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class UserTest extends TestCase
{

    use RefreshDatabase;

    const ADMIN_CODE = 100;
    const DRIVER_CODE = 10;
    const SHIPPER_CODE = 20;
    const GUEST_CODE = 0;


    public function test_get_correct_user_types()
    {
        $admin = factory(User::class)->create(['admin'=>self::ADMIN_CODE]);
        $this->assertEquals($admin->type,self::ADMIN_CODE);

        $shipper = factory(User::class)->create();
        $shipper->shipper()->create();
        $this->assertEquals($shipper->type,self::SHIPPER_CODE);
        $this->assertNotEquals($shipper->type,self::DRIVER_CODE);
        $this->assertNotEquals($shipper->type,self::ADMIN_CODE);
        $this->assertNotEquals($shipper->type,self::GUEST_CODE);

        $driver = factory(User::class)->create();
        $driver->driver()->create();
        $this->assertEquals($driver->type,self::DRIVER_CODE);


        $default = factory(User::class)->create();
        $this->assertEquals($default->type,self::GUEST_CODE);

    }


}
