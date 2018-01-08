<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
use App\Models\Load;
use App\Models\ShipperLocation;
use App\Models\Pass;
use App\Models\Shipper;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\TruckMake;
use App\Models\TruckModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DriverTrucksTest extends TestCase
{

    use RefreshDatabase;


    public function test_driver_can_create_truck()
    {
        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $truckMake = factory(TruckMake::class)->create();
        $truckModel = factory(TruckModel::class)->create();

        $body = [
            'make_id'  => $truckMake->id,
            'model_id' => $truckModel->id,
            'registration_number' => '21212',
            'registration_expiry' => '2017-09-17',
            'plate_number' => '22222',
            'max_weight' => '22222',
            'year' => '2010',
        ];

        $response = $this->json('POST', '/api/driver/trucks', $body, $header);

        $response->assertJson(['success'=>true]);

        $this->assertDatabaseHas('trucks',array_merge($body));

    }


    public function test_driver_can__update_truck()
    {
        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $truck = factory(Truck::class)->create();
        $truckMake = factory(TruckMake::class)->create();
        $truckModel = factory(TruckModel::class)->create();

        $body = [
            'make_id'  => $truckMake->id,
            'model_id' => $truckModel->id,
            'registration_number' => '21212',
            'registration_expiry' => '2017-09-17',
            'plate_number' => '22222',
            'max_weight' => '22222',
            'year' => '2010',
        ];

        $response = $this->json('POST', '/api/driver/trucks', $body, $header);

        $response->assertJson(['success'=>true]);

        $this->assertDatabaseHas('trucks',array_merge($body,['id' => $truck->id]));

    }
}
