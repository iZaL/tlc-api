<?php

namespace Tests\Feature\Driver;

use App\Models\Driver;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverJobsTest extends TestCase
{

//    use DatabaseMigrations;
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

        $validLoad = $this->_createLoad([
            'trailer_id' => 2,
            'origin_location_id' => 1,
            'destination_location_id' => 2,
            'load_date' => Carbon::now()->addDays(1)->toDateString(),
        ]);


        $expiredLoad = $this->_createLoad([
            'trailer_id' => 2,
            'origin_location_id' => 1,
            'destination_location_id' => 2,
            'load_date' => Carbon::now()->subDays(1)->toDateString(),
        ]);

//        $job1= $expiredLoad->jobs()->create(['driver_id' => $driver->id]);
//        $job2 = $validLoad->jobs()->create(['driver_id' => $driver->id]);

        $validJob = factory(Job::class)->create(['load_id'=>$validLoad->id,'driver_id'=>$driver->id]);
        $expiredJob = factory(Job::class)->create(['load_id'=>$expiredLoad->id,'driver_id'=>$driver->id]);

        $response->assertJson(['success'=>true,'data'=>[['id'=>$validJob->id]]]);
        $response->assertJsonMissing(['data'=>[['id'=>$expiredJob->id]]]);

    }



}
