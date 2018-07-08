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
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerEmployeeTest extends TestCase
{

    use RefreshDatabase;

    public function test_customer_gets_employees()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);
        $response = $this->json('GET', '/api/customer/employees', [], $header);
        $response->assertJson(['success'=>true]);
    }


    public function test_customer_create_employee()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $employee = factory(Employee::class)->create();

        $data = [
            'id' => $employee->id,
            'name' => 'wa',
            'mobile' => 99999999,
            'phone' => 99999999,
            'email' => 'z4ls@a.com',
        ];

        $response = $this->json('POST', '/api/customer/employees', $data, $header);
        $response->assertJson(['success'=>true]);
    }



}
