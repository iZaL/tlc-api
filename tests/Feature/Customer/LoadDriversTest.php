<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\CustomerLocation;
use App\Models\SecurityPass;
use App\Models\Trailer;
use App\Models\Truck;
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
     * who has valid trailer === done
     * who has truck registered on same country as load origin country === done
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
     * valid trailer
     * valid route
     */
    public function test_customer_gets_list_of_drivers()
    {
        $customer = $this->_createCustomer();

        $sa = $this->_createCountry('SA',['gcc' => 1]);
        $bh = $this->_createCountry('BH',['gcc' => 1]);
        $in = $this->_createCountry('IN');
        $kw = $this->_createCountry('KW',['gcc' => 1]);

        $origin = factory(CustomerLocation::class)->create(['country_id' => $kw->id, 'customer_id' => $customer->id]);
        $destination = factory(CustomerLocation::class)->create(['country_id' => $bh->id, 'customer_id' => $customer->id]);

        $trailer = factory(Trailer::class)->create();

        // Load
        $load = $this->_createLoad(
            [
                'customer_id'             => $customer->id,
                'origin_location_id'      => $origin->id,
                'destination_location_id' => $destination->id,
                'trailer_id'              => $trailer->id
            ]
        );

        // Route
        $route = $this->_createRoute($origin->country,$destination->country,['transit1'=>$sa->id]);

        // Truck
        $truck1= factory(Truck::class)->create(['registration_country_id'=>$kw->id,'trailer_id'=>$trailer->id]);

        $truck2= factory(Truck::class)->create(['registration_country_id'=>$kw->id,'trailer_id'=>$trailer->id]);

        // Driver
        $driver1 = $this->_createDriver([
            'truck_id' => $truck1->id
        ]);

        $driver2 = $this->_createDriver([
            'truck_id' => $truck2->id
        ]);

        // Residencies
        $driver1->documents()->create(['type'=>'residency','country_id'=>$kw->id]);
        $driver1->documents()->create(['type'=>'residency','country_id'=>$sa->id]);

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
        $pass1 = factory(SecurityPass::class)->create(['country_id' =>$destination->country->id]);
        $pass2 = factory(SecurityPass::class)->create(['country_id' =>$destination->country->id]);

        $load->security_passes()->sync([$pass1->id]);
        $load->security_passes()->sync([$pass2->id]);

        $driver1->security_passes()->sync([$pass1->id]);
        $driver2->security_passes()->sync([$pass1->id]);

        $driver1->security_passes()->sync([$pass2->id]);
        $driver2->security_passes()->sync([$pass2->id]);

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
//                     ['id' => $driver3->id],
                 ]
            ]);

    }


}
