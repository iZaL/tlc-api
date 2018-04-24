<?php

namespace Tests\Feature\Driver;

use App\Managers\TripManager;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class TripManagerTest extends TestCase
{

    use RefreshDatabase;

    protected static function getMethod($name) {
        $class = new ReflectionClass('\App\Managers\TripManager');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @expectedException \App\Exceptions\Load\LoadExpiredException
     */
    public function test_is_load_date_lesser_than_today()
    {
        $loadDate = Carbon::now()->subDays(1);
        $load = $this->_createLoad(['load_date' => $loadDate ]);
        $driver = $this->_createDriver();
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>$driver->id]);
        $method = self::getMethod('isLoadExpired');
        $tripManager = new TripManager($trip,$driver);
        $method->invokeArgs($tripManager,[]);
    }

    /**
     * @expectedException \App\Exceptions\Driver\TLCBlockedException
     */
    public function test_is_driver_blocked()
    {
        $loadDate = Carbon::now()->addDays(1);
        $load = $this->_createLoad(['load_date' => $loadDate ]);
        $driver = $this->_createDriver([
            'blocked' => 1
        ]);
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>$driver->id]);
        $method = self::getMethod('isDriverBlocked');
        $tripManager = new TripManager($trip,$driver);
        $method->invokeArgs($tripManager,[]);
    }

    /**
     * @expectedException \App\Exceptions\Driver\CustomerBlockedException
     */
    public function test_is_driver_blocked_by_customer()
    {
        $customer = $this->_createCustomer();
        $load = $this->_createLoad(['customer_id'=>$customer->id]);
        $driver = $this->_createDriver();
        $driver->blocked_list()->sync([$customer->id]);
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>$driver->id]);
        $method = self::getMethod('isDriverBlockedByCustomer');
        $tripManager = new TripManager($trip,$driver);
        $method->invokeArgs($tripManager,[]);
    }

    /**
     * @expectedException \App\Exceptions\Driver\DuplicateTripException
     */
    public function test_has_duplicate_trip()
    {
        $load = $this->_createLoad();
        $driver = $this->_createDriver();
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>$driver->id,'status' => Trip::STATUS_CONFIRMED]);

        $method = self::getMethod('hasDuplicateTrip');
        $tripManager = new TripManager($trip,$driver);
        $method->invokeArgs($tripManager,[]);
    }

    /**
     * @expectedException \App\Exceptions\Driver\FleetsBookedException
     */
    public function test_is_load_fleets_booked_throws_exception_if_fleet_counts_are_booked()
    {
        $load = $this->_createLoad(['fleet_count'=>4]);
        $driver = $this->_createDriver();
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>222,'status' => Trip::STATUS_CONFIRMED]);
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>111,'status' => Trip::STATUS_CONFIRMED]);
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>122,'status' => Trip::STATUS_ENROUTE]);
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>13311,'status' => Trip::STATUS_COMPLETED]);
        $method = self::getMethod('isLoadFleetsBooked');
        $tripManager = new TripManager($trip,$driver);
        $method->invokeArgs($tripManager,[]);
    }

    public function test_is_load_fleets_booked_returns_false_if_fleet_book_is_not_booked()
    {
        $load = $this->_createLoad(['fleet_count'=>3]);
        $load2 = $this->_createLoad(['fleet_count' =>3]);
        $driver = $this->_createDriver();

        $differentTrip = factory(Trip::class)->create(['load_id'=>$load2->id,'driver_id'=>222,'status' => Trip::STATUS_CONFIRMED]);

        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>222,'status' => Trip::STATUS_CONFIRMED]);
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>222,'status' => Trip::STATUS_ENROUTE]);

        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>222,'status' => Trip::STATUS_PENDING]);
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>222,'status' => Trip::STATUS_PENDING]);

        $method = self::getMethod('isLoadFleetsBooked');
        $tripManager = new TripManager($trip,$driver);
        $try = $method->invokeArgs($tripManager,[]);
        $this->assertFalse($try);
    }

    /**
//     * @expectedException \App\Exceptions\Driver\BusyOnScheduleException
     * Driver's Available Date is booked for the load date
     */
    public function test_driver_has_another_trip()
    {
        $loadDate = Carbon::now()->addDays(5)->toDateString();
        $load = $this->_createLoad(['load_date' => $loadDate ]);

        $driver = $this->_createDriver();
        $this->_makeDriverBusy($driver);

        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>$driver]);
        $method = self::getMethod('driverHasAnotherTrip');
        $tripManager = new TripManager($trip,$driver);
        $method->invokeArgs($tripManager,[]);
    }

    public function test_driver_can_book_if_he_does_not_have_another_trip_on_load_date()
    {
        $loadDate = Carbon::now()->addDays(2)->toDateString();
        $load = $this->_createLoad(['load_date' => $loadDate ]);

        $driver = $this->_createDriver();
        $this->_makeDriverBusy($driver);

        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>$driver]);
        $method = self::getMethod('driverHasAnotherTrip');
        $tripManager = new TripManager($trip,$driver);
        $try = $method->invokeArgs($tripManager,[]);
        $this->assertFalse($try);
    }

}
