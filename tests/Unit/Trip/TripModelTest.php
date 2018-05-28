<?php

namespace Tests\Feature\Driver;

use App\Managers\TripManager;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class TripModelTest extends TestCase
{

    use RefreshDatabase;

    public function test_is_load_date_lesser_than_today()
    {
        $loadDate = Carbon::now()->subDays(1);
        $load = $this->_createLoad(['load_date' => $loadDate ]);
        $driver = $this->_createDriver();
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>$driver->id,'rate' => 300]);

        dd($trip->rate_formatted);
        $rate = round(currency(300,'USD', 'KWD'));
        $this->assertEquals($rate,round($trip->rate));

    }



}
