<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverDocument;
use App\Models\Employee;
use App\Models\Load;
use App\Models\Location;
use App\Models\SecurityPass;
use App\Models\Customer;
use App\Models\CustomerLocation;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerLocationTest extends TestCase
{

    use RefreshDatabase;

    public function test_customer_gets_locations()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $location = factory(CustomerLocation::class)->create(['customer_id'=>$customer->id]);
        $response = $this->json('GET', '/api/customer/locations', [], $header);
        $response->assertJson(['success'=>true,'data'=>['locations'=>[['id'=>$location->id]]]]);
    }

    public function test_customer_creates_location()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $country = factory(Country::class)->create();

        $data = [
            'customer_id' => $customer->id,
            'country' => $country->abbr,
            'latitude' => 27.9,
            'longitude' => 23.1,
            'city' => 'city name',
            'state' => 'state name',
            'address' => 'address address',
            'type' => 'origin',
        ];

        $response = $this->json('POST', '/api/customer/addresses', $data, $header);
        $response->assertJson(['success'=>true]);
        $this->assertDatabaseHas('customer_locations',[
            'customer_id' => $customer->id,
            'country_id' => $country->id,
            'latitude' => 27.9,
            'longitude' => 23.1,
            'city_en' => 'city name',

        ]);
    }



}
