<?php

namespace Tests\Feature\Driver;

use App\Http\Managers\TripManager;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class TripManagerTest extends TestCase
{

    use RefreshDatabase;

    protected static function getMethod($name) {
        $class = new ReflectionClass('\App\Http\Managers\TripManager');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @expectedException \App\Exceptions\Driver\TLCBlockedException
     */
    public function test_is_driver_blocked()
    {
        $loadDate = Carbon::now()->addDays(1);
        $availableDate = Carbon::now()->addDays(2);
        $load = $this->_createLoad(['load_date' => $loadDate ]);
        $driver = $this->_createDriver([
            'available_from' => $availableDate,
            'blocked' => 1
        ]);
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>$driver->id]);
        $method = self::getMethod('checkIsDriverBlocked');
        $tripManager = new TripManager($trip,$driver);
        $method->invokeArgs($tripManager,[]);
    }

    /**
     * @expectedException \App\Exceptions\Driver\ShipperBlockedException
     */
    public function test_is_driver_blocked_by_shipper()
    {
        $shipper = $this->_createShipper();
        $load = $this->_createLoad(['shipper_id'=>$shipper->id]);
        $driver = $this->_createDriver();
        $driver->blocked_list()->sync([$shipper->id]);
        $trip = factory(Trip::class)->create(['load_id'=>$load->id,'driver_id'=>$driver->id]);
        $method = self::getMethod('checkIsDriverBlockedByShipper');
        $tripManager = new TripManager($trip,$driver);
        $method->invokeArgs($tripManager,[]);
    }



}
