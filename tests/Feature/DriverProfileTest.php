<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
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

class DriverProfileTest extends TestCase
{

    use RefreshDatabase;


    public function test_driver_can_update_profile()
    {
        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $body = [
            'mobile'                 => str_random(8),
            'nationality_country_id' => 1,
            'residence_country_id'   => 1
        ];

        $response = $this->json('POST', '/api/driver/profile/update', $body, $header);

        $response->assertJson(['success'=>true]);

        $this->assertDatabaseHas('drivers',array_merge($body,['id'=>$driver->id]));

    }


}
