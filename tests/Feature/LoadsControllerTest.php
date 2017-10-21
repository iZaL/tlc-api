<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverVisas;
use App\Models\Load;
use App\Models\Location;
use App\Models\Shipper;
use App\Models\Trailer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoadsControllerTest extends TestCase
{

    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
    }

    public function test_driver_can_only_see_loads_for_the_country_his_visa_is_not_expired()
    {
        // get loads where origin is country id
        // get destination where id is in valid_visas

        $kw = $this->_createCountry('KW');
        $sa = $this->_createCountry('SA');
        $bh = $this->_createCountry('BH');

        factory(Location::class)->create(['country_id'=>$kw->id]);
        factory(Location::class)->create(['country_id'=>$sa->id]);
        factory(Location::class)->create(['country_id'=>$bh->id]);

        $loadKWKW1 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWSA1 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $sa->id,
        ]);

        $loadKWBH1 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $bh->id,
        ]);

        $loadKWBH2 = factory(Load::class)->states('pending')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $bh->id,
        ]);

        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $visaKw = factory(DriverVisas::class)->create(
            [
                'driver_id'   => $driver->id,
                'country_id'  => $kw->id,
                'expiry_date' => Carbon::now()->addYear(1)->toDateString()
            ]);

        $visasa = factory(DriverVisas::class)->create(
            [
                'driver_id'   => $driver->id,
                'country_id'  => $sa->id,
                'expiry_date' => Carbon::now()->subYear(1)->toDateString()
            ]);

        $visabh = factory(DriverVisas::class)->create(
            [
                'driver_id'   => $driver->id,
                'country_id'  => $bh->id,
                'expiry_date' => Carbon::now()->addYear(1)->toDateString()
            ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/loads', ['current_country' => 'KW'], $header);

        $response->assertJson([
            'data' => [['id'=>$loadKWKW1->id],['id'=>$loadKWBH1->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id'=>$loadKWSA1->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id'=>$loadKWBH2->id]]
        ]);


    }

    public function test_shipper_is_a_user()
    {
        $shipper = factory(Shipper::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);
        $this->assertInstanceOf(User::class, $shipper->user);
    }

    public function test_shipper_can_book_load()
    {
        $shipper = $this->_createShipper();
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);
        $loadData = $this->_createLoadPostData();
        $others = [];
        $postData = array_merge($loadData, $others);

        $response = $this->json('POST', '/api/loads', $postData, $header);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'type'    => 'created'
            ]);
        $this->assertDatabaseHas('loads', $loadData);

    }

    public function test_load_has_status_pending_if_shipper_cannot_book_direct()
    {
        $shipper = $this->_createShipper(['book_direct' => 0]);
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);
        $loadData = $this->_createLoadPostData();
        $others = ['status' => 'pending'];
        $postData = array_merge($loadData, $others);
        $this->json('POST', '/api/loads', $loadData, $header);
        $this->assertDatabaseHas('loads', $postData);
    }

    public function test_load_has_status_approved_if_shipper_can_book_direct()
    {
        $shipper = $this->_createShipper(['book_direct' => 1]);
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);
        $loadData = $this->_createLoadPostData();
        $others = ['status' => 'approved'];
        $postData = array_merge($loadData, $others);
        $this->json('POST', '/api/loads', $loadData, $header);
        $this->assertDatabaseHas('loads', $postData);
    }


    /**
     * Status of Loads
     *
     * 1- Shipper Books a Load, Status = Pending
     * 2- If Can Book Direct, Then Status = Waiting or Once Admin Approves, Status = Waiting
     * 3- If Admin Cancels, Status = Cancelled
     * 4- Driver Confirms the Booking, Status = Confirmed
     * 5- Driver Cancels the Booking, Status = Waiting
     * 6- Driver Starts The mission on Scheduled Date, Status = Started
     * 7- Driver Ends the mission successfully, Status = Completed
     */

    /**
     *
     * Shipper Books Load
     *
     * If TLC has not assigned a direct book for the Shipper for given origin, destn, then make the status of load pending
     * until TLC has assigned an amount for the Load, Amount is Per Truck.
     * If the available credit is low do not allow for load's approval
     *
     * Once Load is Approved, make the load available in the search results
     *
     *
     */

//    public function test_drivers_can_search_valid_loads()
//    {
//        // create a valid load for driver
//        $load = factory(Load::class)->create([
//            'shipper_id'  => 1, 'trailer_id' => 1, 'origin_location_id' => 1, 'destination_location_id' => 2,
//            'fleet_count' => 1, 'price' => 200, 'fixed_rate' => 0, 'status' => 'waiting'
//        ]);
//
//        $driver = $this->_createDriver(['active' => 1]);
//        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
//        $response = $this->json('GET', '/api/loads', [], $header);
//        $response->assertJson([
//            'success' => true,
//            'data'    => [['id' => 1]]
//        ]);
//
////        // make loads with three quantity
////        $load = $this->_createLoad();
////
////        // 1 - Check the Driver is Active // done
////
////        // 2 - Check The Driver has Valid Trailer
////
////        // 3 - Check The Driver has Valid Visa for Destination, Origin // done
////
////        // 4 - Check The Driver has Valid Pass
////
////        // 5 - Check Driver has a Valid License and has not Expired
////
////        // 6 - Check Whether the Shipper has put the Driver/Truck on Blocked List
////
////        // 7 - Check the Status of the Driver, only the Driver with Status 'Available' will be allowed to Book the Load
////
////        // 8 - Check Whether the Driver belongs to Shipper Company If USE OWN TRUCK option is selected by the Shipper
////
////        // 9 - Show Only Loads That has a Status of Approved
////        dd($load);
////
////        $driver = $this->_createDriver();
////
//    }
}
