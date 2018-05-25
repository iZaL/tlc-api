<?php

namespace Tests\Feature\Driver;

use App\Models\Driver;
use App\Models\Load;
use App\Models\CustomerLocation;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverUpcomingLoadsTest extends TestCase
{

    use RefreshDatabase;

    public function test_driver_gets_upcoming_load()
    {

        // get loads where origin is country id
        // get destination where id is in valid_visas

        $kw = $this->_createCountry('KW');

        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $customer1 = $this->_createCustomer();

        $loadKWKW1 = factory(Load::class)->create([
            'customer_id'              => $customer1->id,
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'use_own_truck'           => 0,
            'status' => Load::STATUS_ENROUTE
        ]);

        $loadKWKW1->trips()->create([
            'driver_id' => $driver->id,
            'status' => Trip::STATUS_ENROUTE
        ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/driver/loads/current', [],$header);


        $response->assertJson([
            'data' => ['id' => $loadKWKW1->id]
        ]);
    }

}
