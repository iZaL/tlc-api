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

        $response->assertJson([
            'success' => true,
            'load' => ['id' =>$load->id],
            'trip' => [
                'id' => $trip->id,
                'driver'=>[
                    'id'=>$trip->driver->id,
                    'truck' => [
                        'id' => $driver->truck->id,
                        'trailer' => [
                            'id' => $driver->truck->trailer->id,
                            'type' => ['id' => $driver->truck->trailer->type->id],
                        ],
                        'model' => ['id'=>$driver->truck->model->id],
                        'registration_country' => ['id'=>$driver->truck->registration_country->id],
                    ],
                    'nationalities' => []
                ]
            ]
        ]);

    }

}
