<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
use App\Models\Job;
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

class DriverJobsTest extends TestCase
{

    use RefreshDatabase;

    public function test_driver_gets_jobs_requests()
    {
        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/driver/jobs', [], $header);

        $loadValid = $this->_createLoad([
            'trailer_id' => 2,
            'origin_location_id' => 1,
            'destination_location_id' => 2,
            'load_date' => Carbon::now()->addDays(1)->toDateString(),
        ]);

        $loadExpired = $this->_createLoad([
            'trailer_id' => 2,
            'origin_location_id' => 1,
            'destination_location_id' => 2,
            'load_date' => Carbon::now()->subDays(1)->toDateString(),
        ]);

        $job1= $loadExpired->jobs()->create(['driver_id' => $driver->id]);
        $job2 = $loadValid->jobs()->create(['driver_id' => $driver->id]);

        $a  = factory(Job::class)->create();

        dd($a);

        $response->assertJson(['success'=>true,'data'=>[['id'=>$job1->id]]]);
        $response->assertJsonMissing(['data'=>[['id'=>$job2->id]]]);

    }



}
