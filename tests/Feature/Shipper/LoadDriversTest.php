<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
use App\Models\Employee;
use App\Models\Load;
use App\Models\Location;
use App\Models\Packaging;
use App\Models\Pass;
use App\Models\Shipper;
use App\Models\ShipperLocation;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoadDriversTest extends TestCase
{

    use RefreshDatabase;

    public function test_shipper_gets_driver_who_has_valid_trailer()
    {
        $shipper = $this->_createShipper();
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);

        $invalidDriver1 = $this->_createDriver(['truck_id'=>100]);
        $invalidDriver2 = $this->_createDriver(['truck_id'=>110]);


        $trailer = factory(Trailer::class)->create();
        $truck = factory(Truck::class)->create(['trailer_id' => $trailer->id]);
        $validDriver = $this->_createDriver(['truck_id' => 1]);

        $load = $this->_createLoad(['shipper_id' => $shipper->id]);

        $response = $this->json('GET', '/api/shipper/loads/'.$load->id.'/drivers', [], $header);

//        $response->assertJson(['data' => ['trailers' => [['id' => $trailer1->id], ['id' => $trailer2->id]]]]);

        $response->assertJson(['data'=>[['id'=>$validDriver->id]]]);
        $response->assertJsonMissing(['id'=>$invalidDriver1->id]);
        $response->assertJsonMissing(['id'=>$invalidDriver2->id]);

    }



    /**
     * @todo
     */
//
//    public function test_shipper_needs_enough_credits_to_post_a_load()
//    {
//
//    }
//
//
//    public function test_trailer_selected_by_shipper_can_load_the_cargo()
//    {
//        // does it violate the rules of destination and transit countries
//        // RULES : Dimensions, Load Capacity
//    }



}
