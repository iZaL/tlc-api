<?php

namespace Tests\Feature\Driver;

use App\Managers\DriverManager;
use App\Managers\TripManager;
use App\Models\CustomerLocation;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class DriverManagerTest extends TestCase
{

    use RefreshDatabase;


    public function test_customer_gets_only_active_drivers()
    {
        $customer = $this->_createCustomer();

        $invalidDriver1 = $this->_createDriver(['active' => 0]);
        $invalidDriver2 = $this->_createDriver(['active' => 0]);
        $validDriver1 = $this->_createDriver(['active' => 1]);
        $validDriver2 = $this->_createDriver(['active' => 1]);

        $load = $this->_createLoad(['customer_id' => $customer->id]);

        $driverManager = new DriverManager();

        $drivers = $driverManager->getValidDrivers();

        $this->assertContains($validDriver1->id, $drivers);
        $this->assertContains($validDriver2->id, $drivers);
        $this->assertNotContains($invalidDriver1->id, $drivers);
        $this->assertNotContains($invalidDriver2->id, $drivers);
    }

//
    public function test_customer_gets_only_online_drivers()
    {
        $customer = $this->_createCustomer();

        $invalidDriver1 = $this->_createDriver(['offline' => 1]);
        $invalidDriver2 = $this->_createDriver(['offline' => 1]);
        $validDriver1 = $this->_createDriver(['offline' => 0]);
        $validDriver2 = $this->_createDriver(['offline' => 0]);

        $load = $this->_createLoad(['customer_id' => $customer->id]);

        $driverManager = new DriverManager();

        $drivers = $driverManager->getValidDrivers();

        $this->assertContains($validDriver1->id, $drivers);
        $this->assertContains($validDriver2->id, $drivers);
        $this->assertNotContains($invalidDriver1->id, $drivers);
        $this->assertNotContains($invalidDriver2->id, $drivers);
    }

    public function test_drivers_blocked_by_tlc_are_excluded_from_the_list()
    {
        $customer = $this->_createCustomer();

        $invalidDriver1 = $this->_createDriver(['blocked' => 1]);
        $invalidDriver2 = $this->_createDriver(['blocked' => 1]);
        $validDriver1 = $this->_createDriver(['blocked' => 0]);
        $validDriver2 = $this->_createDriver(['blocked' => 0]);


        $load = $this->_createLoad(['customer_id' => $customer->id]);

        $driverManager = new DriverManager();

        $drivers = $driverManager->getValidDrivers();

        $this->assertContains($validDriver1->id, $drivers);
        $this->assertContains($validDriver2->id, $drivers);
        $this->assertNotContains($invalidDriver1->id, $drivers);
        $this->assertNotContains($invalidDriver2->id, $drivers);
    }

        public function test_drivers_who_are_on_other_trips_are_excluded_from_the_list()
    {
        $customer = $this->_createCustomer();

        $invalidDriver1 = $this->_createDriver();
        $invalidDriver2 = $this->_createDriver();

        $validDriver1 = $this->_createDriver();
        $validDriver2 = $this->_createDriver();

        $this->_makeDriverBusy($invalidDriver1);
        $this->_makeDriverBusy($invalidDriver2);

        $loadDate = Carbon::now()->addDays(5)->toDateString();
        $load = $this->_createLoad(['load_date' => $loadDate]);

        $driverManager = new DriverManager();
        $drivers = $driverManager->getDriversWhoHasTrips($loadDate);

        $this->assertContains($invalidDriver1->id, $drivers);
        $this->assertContains($invalidDriver2->id, $drivers);
        $this->assertNotContains($validDriver1->id, $drivers);
        $this->assertNotContains($validDriver2->id, $drivers);

    }

    public function test_drivers_who_are_blocked_by_customer_are_excluded_from_the_list()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $invalidDriver1 = $this->_createDriver();
        $invalidDriver2 = $this->_createDriver();
        $invalidDriver3 = $this->_createDriver();

        $validDriver1 = $this->_createDriver();
        $validDriver2 = $this->_createDriver();


        $invalidDriver1->blocked_list()->sync([$customer->id]);
        $invalidDriver2->blocked_list()->sync([$customer->id]);
        $this->_makeDriverBusy($invalidDriver3);

        $loadDate = Carbon::now()->addDays(5)->toDateString();

        $load = $this->_createLoad(['customer_id' => $customer->id, 'load_date' => $loadDate]);

        $driverManager = new DriverManager();
        $drivers = $driverManager->getDriversWhoAreBlockedByCustomer($load->customer_id);

        $this->assertContains($invalidDriver1->id, $drivers);
        $this->assertContains($invalidDriver2->id, $drivers);
        $this->assertNotContains($validDriver1->id, $drivers);
        $this->assertNotContains($validDriver2->id, $drivers);

    }

    /**
     *
     */
    public function test_drivers_need_visas_to_travel_to_destination_route()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $invalidDriver1 = $this->_createDriver();
        $invalidDriver2 = $this->_createDriver();
        $invalidDriver3 = $this->_createDriver();

        $validDriver1 = $this->_createDriver();
        $validDriver2 = $this->_createDriver();

        $kw = $this->_createCountry('KW');
        $sa = $this->_createCountry('SA');
        $bh = $this->_createCountry('BH');

        $route  = $this->_createRoute($kw,$bh,['transit1'=>$sa->id]);
        $origin = factory(CustomerLocation::class)->create(['country_id' => $kw->id, 'customer_id' => $customer->id]);
        $destination = factory(CustomerLocation::class)->create(['country_id' => $bh->id, 'customer_id' => $customer->id]);

        $this->_createVisa($validDriver1->id, $kw->id);
        $this->_createVisa($validDriver1->id, $sa->id);
        $this->_createVisa($validDriver1->id, $bh->id);

        $this->_createVisa($validDriver2->id, $kw->id);
        $this->_createVisa($validDriver2->id, $sa->id);
        $this->_createVisa($validDriver2->id, $bh->id);

        $this->_createVisa($invalidDriver1->id, $sa->id, false);

        $load = $this->_createLoad(
            [
                'customer_id'             => $customer->id,
                'origin_location_id'      => $origin->id,
                'destination_location_id' => $destination->id,
            ]
        );

        $driverManager = new DriverManager();
        $drivers = $driverManager->getDriversWhoHasValidVisas([$kw->id,$sa->id,$bh->id],$load->load_date);

        $this->assertContains($validDriver1->id, $drivers);
        $this->assertContains($validDriver2->id, $drivers);
        $this->assertNotContains($invalidDriver1->id, $drivers);
        $this->assertNotContains($invalidDriver2->id, $drivers);
        $this->assertNotContains($invalidDriver3->id, $drivers);

    }

    public function test_drivers_need_licenses_to_travel_to_destination_route()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $invalidDriver1 = $this->_createDriver();
        $invalidDriver2 = $this->_createDriver();
        $invalidDriver3 = $this->_createDriver();

        $validDriver1 = $this->_createDriver();
        $validDriver2 = $this->_createDriver();

        $kw = $this->_createCountry('KW');
        $sa = $this->_createCountry('SA');
        $bh = $this->_createCountry('BH');

        $route  = $this->_createRoute($kw,$bh,['transit1'=>$sa->id]);
        $origin = factory(CustomerLocation::class)->create(['country_id' => $kw->id, 'customer_id' => $customer->id]);
        $destination = factory(CustomerLocation::class)->create(['country_id' => $bh->id, 'customer_id' => $customer->id]);

        $this->_createLicense($validDriver1->id, $kw->id);
        $this->_createLicense($validDriver1->id, $sa->id);
        $this->_createLicense($validDriver1->id, $bh->id);

        $this->_createLicense($validDriver2->id, $kw->id);
        $this->_createLicense($validDriver2->id, $sa->id);
        $this->_createLicense($validDriver2->id, $bh->id);

        $this->_createLicense($invalidDriver1->id, $sa->id, false);

        $load = $this->_createLoad(
            [
                'customer_id'             => $customer->id,
                'origin_location_id'      => $origin->id,
                'destination_location_id' => $destination->id,
            ]
        );

        $driverManager = new DriverManager();
        $drivers = $driverManager->getDriversWhoHasValidLicenses([$kw->id,$sa->id,$bh->id],$load->load_date);

        $this->assertContains($validDriver1->id, $drivers);
        $this->assertContains($validDriver2->id, $drivers);
        $this->assertNotContains($invalidDriver1->id, $drivers);
        $this->assertNotContains($invalidDriver2->id, $drivers);
        $this->assertNotContains($invalidDriver3->id, $drivers);

    }


}
