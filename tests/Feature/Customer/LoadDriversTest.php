<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\CustomerLocation;
use App\Models\Pass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoadDriversTest extends TestCase
{

    use RefreshDatabase;


    /** get drivers
     * who are active === done
     * who are not offline === done
     * who are not blocked by customer === done
     * who are not blocked by tlc === done
     * who are not on other trips === done
     * who has valid visas (not expired) to destination country and transit country === done
     * who has valid licenses (not expired) === done
     * who has valid truck, trailer (length,width,height,capacity) depending on the load dimension
     * who has truck registered on same country as load origin country
     * who has added the load route in their route list === done
     * who has valid gate passes to the load destination if required === done
     * who works for same customer if customer prefers their own driver
     *
     * ====================
     *
     * valid passes
     * valid visas
     * valid licenses
     * active
     * not offline
     * not blocked by tlc
     * not blocked by customer
     * not on other trips
     * valid truck, trailer
     * valid route
     */
    public function test_customer_gets_list_of_drivers()
    {
        $customer = $this->_createCustomer();

        $kw = $this->_createCountry('KW',['gcc' => 1]);
        $sa = $this->_createCountry('SA',['gcc' => 1]);
        $bh = $this->_createCountry('BH',['gcc' => 1]);
        $in = $this->_createCountry('IN');

        $origin = factory(CustomerLocation::class)->create(['country_id' => $kw->id, 'customer_id' => $customer->id]);
        $destination = factory(CustomerLocation::class)->create(['country_id' => $bh->id, 'customer_id' => $customer->id]);

        // Load
        $load = $this->_createLoad(
            [
                'customer_id'             => $customer->id,
                'origin_location_id'      => $origin->id,
                'destination_location_id' => $destination->id,
            ]
        );

        // Route
        $route = $this->_createRoute($origin->country,$destination->country,['transit1'=>$sa->id]);

        // Driver
        $driver1 = $this->_createDriver([
            'nationality_country_id' => $in->id,
        ]);

        $driver2 = $this->_createDriver([
            'nationality_country_id' => $kw->id
        ]);

        // Residencies
        $driver1->residencies()->sync([$kw->id]);
        $driver1->residencies()->sync([$kw->id,$sa->id]);

        //Routes
        $driver1->routes()->sync([$route->id]);
        $driver2->routes()->sync([$route->id]);

        //Visas
        $this->_createVisa($driver1->id, $kw->id);
        $this->_createVisa($driver1->id, $sa->id);
        $this->_createVisa($driver1->id, $bh->id);

        $this->_createVisa($driver2->id, $kw->id);
        $this->_createVisa($driver2->id, $sa->id);
        $this->_createVisa($driver2->id, $bh->id);

        // Licenses
        $this->_createLicense($driver1->id,$kw->id);
        $this->_createLicense($driver1->id,$sa->id);
        $this->_createLicense($driver1->id,$bh->id);

        $this->_createLicense($driver2->id,$kw->id);
        $this->_createLicense($driver2->id,$sa->id);
        $this->_createLicense($driver2->id,$bh->id);

        // Passes
        $pass1 = factory(Pass::class)->create(['country_id'=>$destination->country->id]);
        $pass2 = factory(Pass::class)->create(['country_id'=>$destination->country->id]);

        $load->passes()->sync([$pass1->id]);
        $load->passes()->sync([$pass2->id]);

        $driver1->passes()->sync([$pass1->id]);
        $driver2->passes()->sync([$pass1->id]);

        $driver1->passes()->sync([$pass2->id]);
        $driver2->passes()->sync([$pass2->id]);


        // Request
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);
        $response = $this->json('GET', '/api/customer/loads/' . $load->id . '/drivers/search', [], $header);

        // Response
        $response->assertJson(
            [
                'data' =>
                 [
                     ['id' => $driver1->id],
                     ['id' => $driver2->id],
                 ]
            ]);

    }


}
