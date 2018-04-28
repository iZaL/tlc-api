<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverDocument;
use App\Models\Employee;
use App\Models\Load;
use App\Models\Location;
use App\Models\Packaging;
use App\Models\SecurityPass;
use App\Models\Customer;
use App\Models\CustomerLocation;
use App\Models\Trailer;
use App\Models\TrailerType;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTripTest extends TestCase
{

    use RefreshDatabase;

    public function test_customer_gets_trip_details_by_id()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $driver = $this->_createDriver([],['truck'=>1]);
        $load = $this->_createLoad();
        $trip = $this->_createTrip(['load_id'=>$load->id,'driver_id'=>$driver->id]);


        $response = $this->json('GET', '/api/customer/trips/'.$trip->id.'/details', [], $header);

        $response->assertJson(['success' => true]);
//        $response->assertJson(['data' => ['trailers' => [['id' => $trailer1->id], ['id' => $trailer2->id]]]]);
//        $response->assertJson(['data' => ['packaging' => [['id' => $packaging1->id], ['id' => $packaging2->id]]]]);

    }

//
//    /**
//     * @todo
//     */
//
//    public function test_customer_needs_enough_credits_to_post_a_load()
//    {
//
//    }
//
//
//    public function test_trailer_selected_by_customer_can_load_the_cargo()
//    {
//        // does it violate the rules of destination and transit countries
//        // RULES : Dimensions, Load Capacity
//    }



}
