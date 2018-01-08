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

        $validJob= $expiredLoad->trips()->create(['driver_id' => $driver->id]);
        $expiredJob = $validLoad->trips()->create(['driver_id' => $driver->id]);

        $response = $this->json('GET', '/api/driver/trips', [], $header);

        $response->assertJson(['success'=>true,'data'=>[['id'=>$validJob->id]]]);
        $response->assertJsonMissing(['data'=>[['id'=>$expiredJob->id]]]);

    }


    public function test_driver_trips_excludes_trips_()
    {
        $driver = $this->_createDriver();

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $validLoad = $this->_createLoad([
            'load_date' => Carbon::now()->addDays(1)->toDateString(),
        ]);

        $expiredLoad = $this->_createLoad([
            'load_date' => Carbon::now()->subDays(1)->toDateString(),
        ]);

        $validJob= $expiredLoad->trips()->create(['driver_id' => $driver->id]);
        $expiredJob = $validLoad->trips()->create(['driver_id' => $driver->id]);

        $response = $this->json('GET', '/api/driver/trips', [], $header);

        $response->assertJson(['success'=>true,'data'=>[['id'=>$validJob->id]]]);
        $response->assertJsonMissing(['data'=>[['id'=>$expiredJob->id]]]);

    }

}
