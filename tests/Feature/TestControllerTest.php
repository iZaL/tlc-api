<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestControllerTest extends TestCase
{

    use RefreshDatabase;

    const ADMIN_CODE = 100;
    const DRIVER_CODE = 10;
    const CUSTOMER_CODE = 20;
    const GUEST_CODE = 0;

//
//    public function test_get_correct_user_types()
//    {
//        $response = $this->json('GET', '/test');
//
//    }


}
