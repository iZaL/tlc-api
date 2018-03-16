<?php

namespace Tests\Feature\Driver;

use App\Managers\DriverManager;
use App\Managers\RouteManager;
use App\Managers\TripManager;
use App\Models\CustomerLocation;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class RouteManagerTest extends TestCase
{

    use RefreshDatabase;

    /**
     *
     */
    public function test_get_route_transit_countries()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $kw = $this->_createCountry('KW');
        $sa = $this->_createCountry('SA');
        $bh = $this->_createCountry('BH');
        $om = $this->_createCountry('OM');

        $route  = $this->_createRoute($kw,$bh,['transit1'=>$sa->id]);
        $origin = factory(CustomerLocation::class)->create(['country_id' => $kw->id, 'customer_id' => $customer->id]);
        $destination = factory(CustomerLocation::class)->create(['country_id' => $bh->id, 'customer_id' => $customer->id]);

        $load = $this->_createLoad(
            [
                'customer_id'             => $customer->id,
                'origin_location_id'      => $origin->id,
                'destination_location_id' => $destination->id,
            ]
        );

        $manager = new RouteManager();
        $countries = $manager->getRouteCountries($origin->country->id,$destination->country->id);

        $this->assertContains($kw->id, $countries);
        $this->assertContains($sa->id, $countries);
        $this->assertContains($bh->id, $countries);
        $this->assertNotContains($om->id, $countries);

    }

    public function test_get_drivers_for_route()
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

        $validDriver1->routes()->sync([$route->id]);
        $validDriver2->routes()->sync([$route->id]);

        $load = $this->_createLoad(
            [
                'customer_id'             => $customer->id,
                'origin_location_id'      => $origin->id,
                'destination_location_id' => $destination->id,
            ]
        );

        $manager = new RouteManager();
        $drivers = $manager->getRouteDrivers($kw->id,$bh->id);

        $this->assertContains($validDriver1->id, $drivers);
        $this->assertContains($validDriver2->id, $drivers);
        $this->assertNotContains($invalidDriver1->id, $drivers);
        $this->assertNotContains($invalidDriver2->id, $drivers);
        $this->assertNotContains($invalidDriver3->id, $drivers);

    }

}
