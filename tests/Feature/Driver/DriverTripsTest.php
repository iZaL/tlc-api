<?php

namespace Tests\Feature\Driver;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverTripsTest extends TestCase
{

    use RefreshDatabase;


    public function test_driver_trips_excludes_expired_jobs()
    {
        $driver = $this->_createDriver();

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $validLoad = $this->_createLoad([
            'load_date' => Carbon::now()->addDays(1)->toDateString(),
        ]);

        $expiredLoad = $this->_createLoad([
            'load_date' => Carbon::now()->subDays(1)->toDateString(),
        ]);

        $validTrip = $validLoad->trips()->create(['driver_id' => $driver->id]);
        $expiredTrip = $expiredLoad->trips()->create(['driver_id' => $driver->id]);

        $response = $this->json('GET', '/api/driver/trips', [], $header);

        $response->assertJson(['success'=>true,'data'=>[['id'=>$validTrip->id]]]);

        $missingJsonFragment = ['id'=>$expiredTrip->id];

        $response->assertJsonMissing($missingJsonFragment);

    }

    public function test_driver_can_confirm_valid_trip()
    {
        $driver = $this->_createDriver();

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $loadDate= Carbon::now()->addDays(1)->toDateString();
        $validLoad = $this->_createLoad([
            'load_date' => $loadDate,
        ]);

        $validTrip = $validLoad->trips()->create(['driver_id' => $driver->id]);

        $response = $this->json('POST', '/api/driver/trips/'.$validTrip->id.'/confirm', [], $header);

        $this->assertDatabaseHas('trips',['id'=>$validTrip->id,'status'=>'confirmed']);
        $this->assertDatabaseHas('driver_blocked_dates',['driver_id'=>$driver->id,'from'=>$loadDate]);

    }

    public function test_driver_cannot_confirm_invalid_trip()
    {
        $driver = $this->_createDriver();

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $loadDate = Carbon::now()->addDays(5)->toDateString();

        $validLoad = $this->_createLoad([
            'load_date' => $loadDate,
        ]);

        $bookedFrom = Carbon::now()->addDays(3)->toDateString();

        $bookedUntil = Carbon::now()->addDays(6)->toDateString();

        $driver->blocked_dates()->create(['from' => $bookedFrom, 'to' => $bookedUntil ]);

        $validTrip = $validLoad->trips()->create(['driver_id' => $driver->id]);

        $response = $this->json('POST', '/api/driver/trips/'.$validTrip->id.'/confirm', [], $header);

        $response->assertJson(['success'=>false,'message'=>'driver_has_trip']);

        $this->assertDatabaseHas('trips',['id'=>$validTrip->id,'status'=>'pending']);

    }

    public function test_driver_cannot_book_trip_for_approved_loads()
    {
        $driver = $this->_createDriver();

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $load = $this->_createLoad([
            'status' => 'confirmed'
        ]);

        $validTrip = $load->trips()->create(['driver_id' => $driver->id]);

        $response = $this->json('POST', '/api/driver/trips/'.$validTrip->id.'/confirm', [], $header);

        $response->assertJson(['success'=>false,'message'=>'load_already_confirmed']);

        $this->assertDatabaseHas('trips',['id'=>$validTrip->id,'status'=>'pending']);
    }

    public function test_driver_cannot_book_trip_for_expired_loads()
    {
        $driver = $this->_createDriver();

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $loadDate = Carbon::now()->subDays(5)->toDateString();

        $load = $this->_createLoad([
            'load_date' => $loadDate
        ]);

        $validTrip = $load->trips()->create(['driver_id' => $driver->id]);

        $response = $this->json('POST', '/api/driver/trips/'.$validTrip->id.'/confirm', [], $header);

        $response->assertJson(['success'=>false,'message'=>'load_expired']);

        $this->assertDatabaseHas('trips',['id'=>$validTrip->id,'status'=>'pending']);
    }

    public function test_load_status_gets_updated_after_trip_confirmation()
    {
        $driver = $this->_createDriver();

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $loadDate = Carbon::now()->addDays(5)->toDateString();

        $load = $this->_createLoad([
            'load_date' => $loadDate,
            'fleet_count' => 1
        ]);

        $validTrip = $load->trips()->create(['driver_id' => $driver->id]);

        $response = $this->json('POST', '/api/driver/trips/'.$validTrip->id.'/confirm', [], $header);

        $response->assertJson(['success'=>true]);

        $this->assertDatabaseHas('loads',['id'=>$load->id,'status'=>'confirmed']);
    }

    public function test_load_status_gets_updated_after_trip_confirmation_for_fleet_count()
    {
        $driver = $this->_createDriver();

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $loadDate = Carbon::now()->addDays(5)->toDateString();

        $load = $this->_createLoad([
            'load_date' => $loadDate,
            'fleet_count' => 3
        ]);

        $validTrip1 = $load->trips()->create(['driver_id' => $driver->id]);
        $validTrip2 = $load->trips()->create(['driver_id' => 333, 'status' => 'confirmed']);
        $validTrip2 = $load->trips()->create(['driver_id' => 444, 'status' => 'working']);
        $validTrip3 = $load->trips()->create(['driver_id' => 555, 'status' => 'rejected']);

        $response = $this->json('POST', '/api/driver/trips/'.$validTrip1->id.'/confirm', [], $header);

        $response->assertJson(['success'=>true]);

        $this->assertDatabaseHas('loads',['id'=>$load->id,'status'=>'confirmed']);

    }

}
