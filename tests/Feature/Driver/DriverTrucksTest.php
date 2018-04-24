<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverDocument;
use App\Models\Load;
use App\Models\CustomerLocation;
use App\Models\SecurityPass;
use App\Models\Customer;
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


//        $table->integer('registration_country_id')->nullable();
//        $table->string('registration_number')->nullable();
//        $table->date('registration_expiry_date')->nullable();
//        $table->string('registration_image')->nullable();

        $body = [
//            'make_id'  => $truckMake->id,
            'model_id' => $truckModel->id,
            'registration_country_id' => 1,
            'registration_number' => '21212',
            'registration_expiry_date' => '2018-09-17',
//            'registration_image' => '2018-09-17',
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
//            'make_id'  => $truckMake->id,
            'model_id' => $truckModel->id,
            'registration_number' => '21212',
            'registration_country_id' => 1,
            'registration_expiry_date' => '2018-09-17',
            'plate_number' => '22222',
            'max_weight' => '22222',
            'year' => '2010',
        ];

        $response = $this->json('POST', '/api/driver/trucks', $body, $header);

        $response->assertJson(['success'=>true]);

        $this->assertDatabaseHas('trucks',array_merge($body,['id' => $truck->id]));

    }
}
