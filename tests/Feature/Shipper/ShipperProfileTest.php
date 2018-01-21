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
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShipperProfileTest extends TestCase
{

    use RefreshDatabase;

    public function test_shipper_gets_profile()
    {
        $shipper = $this->_createShipper();
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);
        $response = $this->json('GET', '/api/shipper/profile', [], $header);
        $response->assertJson(['success'=>true]);
    }



}
