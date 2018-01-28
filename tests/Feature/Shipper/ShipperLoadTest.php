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

class ShipperLoadTest extends TestCase
{

    use RefreshDatabase;

    public function test_shipper_gets_load_add_data()
    {
        $shipper = $this->_createShipper();
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);


        $trailer1 = factory(Trailer::class)->create();
        $trailer2 = factory(Trailer::class)->create();

        $packaging1 = factory(Packaging::class)->create();
        $packaging2 = factory(Packaging::class)->create();

        $location = factory(ShipperLocation::class,4)->create(['shipper_id'=>$shipper->id]);

        $response = $this->json('GET', '/api/shipper/loads/add/data', [], $header);

        $response->assertJson(['success'=>true]);
        $response->assertJson(['data'=> ['trailers' => [['id'=>$trailer1->id],['id'=>$trailer2->id]]]]);
        $response->assertJson(['data'=> ['packaging' => [['id'=>$packaging1->id],['id'=>$packaging2->id]]]]);

    }



}
