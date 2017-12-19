<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
use App\Models\Load;
use App\Models\Location;
use App\Models\Pass;
use App\Models\Shipper;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DriverRoutesTestTest extends TestCase
{

    use RefreshDatabase;


    public function test_driver_can_add_route()
    {
        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $body = [
            'route_id' => 1
        ];

        $response = $this->json('POST', '/api/driver/routes', $body, $header);

        $response->assertJson(['success'=>true]);

        $this->assertDatabaseHas('driver_routes',array_merge($body,['driver_id'=>$driver->id]));

    }


}
