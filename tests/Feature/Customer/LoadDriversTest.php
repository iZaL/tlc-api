<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
use App\Models\Employee;
use App\Models\Load;
use App\Models\Location;
use App\Models\Packaging;
use App\Models\Pass;
use App\Models\Customer;
use App\Models\CustomerLocation;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoadDriversTest extends TestCase
{

    use RefreshDatabase;


    private function makeRequest()
    {

    }

    private function _createNationality()
    {
        return factory(Country::class)->create(['name_en' => 'kuwait', 'gcc' => 1]);
    }

    private function _createValidLoad($array = [])
    {

        $data = [];
        $loadData = array_merge($array, $data);
        $load = $this->_createLoad($loadData);
        return $load;
    }

    /** get drivers
     * who are active === done
     * who are not offline === done
     * who are not blocked by customer === done
     * who are not blocked by tlc === done
     * who are not on other trips === done
     * who has valid visas (not expired) to destination country and transit country
     * who has valid licenses (not expired)
     * who has valid truck, trailer (length,width,height,capacity) depending on the load dimension
     * who has truck registered on same country as load origin country
     * who has added the load route in their route list
     * who has valid gate passes to the load destination if required
     * who works for same customer if customer prefers their own driver
     */
    private function _createValidDriver($array = [])
    {
        $nationality = $this->_createNationality();
        $driverData = array_merge($array, ['nationality_country_id' => $nationality->id]);
        $driver = $this->_createDriver($driverData);
        return $driver;
        // valid passes
        // valid visas
        // valid licenses
        // active
        // not offline
        // not blocked by tlc
        // not blocked by customer
        // not on other trips
        // valid truck, trailer
        // valid route
    }


    public function test_customer_gets_only_active_drivers()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $invalidDriver1 = $this->_createValidDriver(['active' => 0]);
        $invalidDriver2 = $this->_createValidDriver(['active' => 0]);
        $validDriver1 = $this->_createValidDriver(['active' => 1]);
        $validDriver2 = $this->_createValidDriver(['active' => 1]);

        $load = $this->_createLoad(['customer_id' => $customer->id]);

        $response = $this->json('GET', '/api/customer/loads/' . $load->id . '/drivers/search', [], $header);

        $response->assertJson(['data' => [['id' => $validDriver1->id], ['id' => $validDriver2->id]]]);
        $response->assertJsonMissing(['id' => $invalidDriver1->id]);
        $response->assertJsonMissing(['id' => $invalidDriver2->id]);
    }

    public function test_customer_gets_only_online_drivers()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $invalidDriver1 = $this->_createValidDriver(['offline' => 1]);
        $invalidDriver2 = $this->_createValidDriver(['offline' => 1]);
        $validDriver1 = $this->_createValidDriver(['offline' => 0]);
        $validDriver2 = $this->_createValidDriver(['offline' => 0]);

        $load = $this->_createLoad(['customer_id' => $customer->id]);

        $response = $this->json('GET', '/api/customer/loads/' . $load->id . '/drivers/search', [], $header);

        $response->assertJson(['data' => [['id' => $validDriver1->id], ['id' => $validDriver2->id]]]);
        $response->assertJsonMissing(['id' => $invalidDriver1->id]);
        $response->assertJsonMissing(['id' => $invalidDriver2->id]);
    }

    public function test_drivers_blocked_by_tlc_are_excluded_from_the_list()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $invalidDriver1 = $this->_createValidDriver(['blocked' => 1]);
        $invalidDriver2 = $this->_createValidDriver(['blocked' => 1]);
        $validDriver1 = $this->_createValidDriver(['blocked' => 0]);
        $validDriver2 = $this->_createValidDriver(['blocked' => 0]);

        $load = $this->_createLoad(['customer_id' => $customer->id]);

        $response = $this->json('GET', '/api/customer/loads/' . $load->id . '/drivers/search', [], $header);

        $response->assertJson(['data' => [['id' => $validDriver1->id], ['id' => $validDriver2->id]]]);
        $response->assertJsonMissing(['id' => $invalidDriver1->id]);
        $response->assertJsonMissing(['id' => $invalidDriver2->id]);
    }

    public function test_drivers_who_are_on_other_trips_are_excluded_from_the_list()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $invalidDriver1 = $this->_createValidDriver();
        $invalidDriver2 = $this->_createValidDriver();

        $validDriver1 = $this->_createValidDriver(['blocked' => 0]);
        $validDriver2 = $this->_createValidDriver(['blocked' => 0]);

        $this->_makeDriverBusy($invalidDriver1);
        $this->_makeDriverBusy($invalidDriver2);


        $loadDate = Carbon::now()->addDays(5)->toDateString();
        $load = $this->_createValidLoad(['load_date' => $loadDate]);

        $response = $this->json('GET', '/api/customer/loads/' . $load->id . '/drivers/search', [], $header);

        $response->assertJson(['data' => [['id' => $validDriver1->id], ['id' => $validDriver2->id]]]);
        $response->assertJsonMissing(['id' => $invalidDriver1->id]);
        $response->assertJsonMissing(['id' => $invalidDriver2->id]);

    }

    public function test_drivers_who_are_blocked_by_customer_are_excluded_from_the_list()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $invalidDriver1 = $this->_createValidDriver();
        $invalidDriver2 = $this->_createValidDriver();
        $invalidDriver3 = $this->_createValidDriver();

        $validDriver1 = $this->_createValidDriver();
        $validDriver2 = $this->_createValidDriver();


        $invalidDriver1->blocked_list()->sync([$customer->id]);
        $invalidDriver2->blocked_list()->sync([$customer->id]);
        $this->_makeDriverBusy($invalidDriver3);

        $loadDate = Carbon::now()->addDays(5)->toDateString();

        $load = $this->_createValidLoad(['customer_id' => $customer->id, 'load_date' => $loadDate]);

        $response = $this->json('GET', '/api/customer/loads/' . $load->id . '/drivers/search', [], $header);

        $response->assertJson(['data' => [['id' => $validDriver1->id], ['id' => $validDriver2->id]]]);
        $response->assertJsonMissing(['id' => $invalidDriver1->id]);
        $response->assertJsonMissing(['id' => $invalidDriver2->id]);
        $response->assertJsonMissing(['id' => $invalidDriver3->id]);

    }

    public function test_drivers_need_visas_to_travel_to_destination_route()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $invalidDriver1 = $this->_createValidDriver();
        $invalidDriver2 = $this->_createValidDriver();
        $invalidDriver3 = $this->_createValidDriver();

        $validDriver1 = $this->_createValidDriver();
        $validDriver2 = $this->_createValidDriver();

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

//        $this->_createVisa($invalidDriver1->id, $kw->id);
        $this->_createVisa($invalidDriver1->id, $sa->id, false);


        $load = $this->_createValidLoad(
            [
                'customer_id'             => $customer->id,
                'origin_location_id'      => $origin->id,
                'destination_location_id' => $destination->id,
            ]
        );

        $response = $this->json('GET', '/api/customer/loads/' . $load->id . '/drivers/search', [], $header);

        $response->assertJson(['data' => [['id' => $validDriver1->id], ['id' => $validDriver2->id]]]);
        $response->assertJsonMissing(['id' => $invalidDriver1->id]);
        $response->assertJsonMissing(['id' => $invalidDriver2->id]);
        $response->assertJsonMissing(['id' => $invalidDriver3->id]);

    }


}
