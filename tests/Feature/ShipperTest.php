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

class ShipperTest extends TestCase
{

    use RefreshDatabase;

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

        $response = $this->json('POST', '/api/shipper/loads', $postData, $header);
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
        $this->json('POST', '/api/shipper/loads', $loadData, $header);
        $this->assertDatabaseHas('loads', $postData);
    }

    public function test_load_has_status_approved_if_shipper_can_book_direct()
    {
        $shipper = $this->_createShipper(['book_direct' => 1]);
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);
        $loadData = $this->_createLoadPostData();
        $others = ['status' => 'approved'];
        $postData = array_merge($loadData, $others);
        $this->json('POST', '/api/shipper/loads', $loadData, $header);
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




}
