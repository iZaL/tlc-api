<?php

namespace Tests\Feature\Driver;

use App\Exceptions\TLCBlockedException;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverConfirmTripTest extends TestCase
{

    use RefreshDatabase;


    /**
     * @expectedException TLCBlockedException
     */
    public function test_driver_cannot_book_when_he_is_blocked_by_tlc()
    {
        $loadDate = Carbon::now()->addDays(1);
        $availableDate = Carbon::now()->addDays(2);
        $load = $this->_createLoad(['load_date' => $loadDate ]);
        $driver = $this->_createDriver([
            'available_from' => $availableDate,
            'blocked' => 1
        ]);

        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>$driver->id]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $response = $this->json('POST', '/api/driver/trips/'.$trip->id.'/confirm', [], $header);

    }



}
