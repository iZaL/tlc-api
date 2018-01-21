<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
use App\Models\Employee;
use App\Models\Load;
use App\Models\Location;
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

class ShipperLocationTest extends TestCase
{

    use RefreshDatabase;

    public function test_shipper_gets_locations()
    {
        $shipper = $this->_createShipper();
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);

        $location = factory(ShipperLocation::class)->create(['shipper_id'=>$shipper->id]);
        $response = $this->json('GET', '/api/shipper/locations', [], $header);
        $response->assertJson(['success'=>true,'data'=>['locations'=>[['id'=>$location->id]]]]);
    }

    public function test_shipper_creates_location()
    {
        $shipper = $this->_createShipper();
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);

        $employee = factory(Employee::class)->create();

        $data = [
            'id' => $employee->id,
            'name_en' => 'wa',
            'name_ar' => 'wss',
            'mobile' => 99999999,
            'phone' => 99999999,
            'email' => 'z4ls@a.com',
        ];

        $response = $this->json('POST', '/api/shipper/employees', $data, $header);
        $response->assertJson(['success'=>true]);
    }



}
