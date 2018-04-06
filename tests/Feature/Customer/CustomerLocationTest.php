<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverDocument;
use App\Models\Employee;
use App\Models\Load;
use App\Models\Location;
use App\Models\Pass;
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

        $employee = factory(Employee::class)->create();
        $data = [
            'id' => $employee->id,
            'name_en' => 'wa',
            'name_ar' => 'wss',
            'mobile' => 99999999,
            'phone' => 99999999,
            'email' => 'z4ls@a.com',
        ];

        $response = $this->json('POST', '/api/customer/employees', $data, $header);
        $response->assertJson(['success'=>true]);
    }



}
