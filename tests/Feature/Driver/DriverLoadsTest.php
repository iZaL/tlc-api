<?php

namespace Tests\Feature\Driver;

use App\Models\Driver;
use App\Models\Load;
use App\Models\SecurityPass;
use App\Models\CustomerLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverLoadsTest extends TestCase
{

    use RefreshDatabase;

    public function test_driver_can_only_see_loads_for_his_customer_if_use_own_truck_option_selected()
    {

        // get loads where origin is country id
        // get destination where id is in valid_visas

        $kw = $this->_createCountry('KW');
        $sa = $this->_createCountry('SA');
        $bh = $this->_createCountry('BH');
        $iq = $this->_createCountry('IQ');

        factory(CustomerLocation::class)->create(['country_id' => $kw->id]);
        factory(CustomerLocation::class)->create(['country_id' => $sa->id]);
        factory(CustomerLocation::class)->create(['country_id' => $bh->id]);
        factory(CustomerLocation::class)->create(['country_id' => $iq->id]);

        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $driver2 = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $customer1 = $this->_createCustomer();
        $customer2 = $this->_createCustomer();
        $customer3 = $this->_createCustomer();
        $customer4 = $this->_createCustomer();

        $driver->customer_id = $customer2->id;
        $driver->update();

        $driver2->customer_id = $customer1->id;
        $driver->update();

        $loadKWKW1 = factory(Load::class)->states('approved')->create([
            'customer_id'              => $customer1->id,
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'use_own_truck'           => 0
        ]);

        $loadKWKW2 = factory(Load::class)->states('approved')->create([
            'customer_id'              => $customer1->id,
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'use_own_truck'           => 1
        ]);

        $loadKWKW3 = factory(Load::class)->states('approved')->create([
            'customer_id'              => $customer2->id,
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'use_own_truck'           => 1
        ]);

        $loadKWKW4 = factory(Load::class)->states('approved')->create([
            'customer_id'              => $customer3->id,
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'use_own_truck'           => 1
        ]);

        $loadKWKW5 = factory(Load::class)->states('approved')->create([
            'customer_id'              => $customer4->id,
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'use_own_truck'           => 1
        ]);

        $this->_createLicense($driver->id, $kw->id);
        $this->_createVisa($driver->id, $kw->id);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/driver/loads', ['current_country' => 'KW'], $header);

        $response->assertJson([
            'data' => [['id' => $loadKWKW1->id], ['id' => $loadKWKW3->id]]
        ]);
    }

    public function test_driver_can_only_see_loads_for_the_customers_who_hasnt_put_him_on_blocked_list()
    {
        // get loads where origin is country id
        // get destination where id is in valid_visas

        $kw = $this->_createCountry('KW');
        $sa = $this->_createCountry('SA');
        $bh = $this->_createCountry('BH');
        $iq = $this->_createCountry('IQ');

        factory(CustomerLocation::class)->create(['country_id' => $kw->id]);
        factory(CustomerLocation::class)->create(['country_id' => $sa->id]);
        factory(CustomerLocation::class)->create(['country_id' => $bh->id]);
        factory(CustomerLocation::class)->create(['country_id' => $iq->id]);

        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $customer1 = $this->_createCustomer();
        $customer2 = $this->_createCustomer();
        $customer3 = $this->_createCustomer();
        $customer4 = $this->_createCustomer();

        $loadKWKW1 = factory(Load::class)->states('approved')->create([
            'customer_id'              => $customer1->id,
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW2 = factory(Load::class)->states('approved')->create([
            'customer_id'              => $customer1->id,
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW3 = factory(Load::class)->states('approved')->create([
            'customer_id'              => $customer2->id,
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW4 = factory(Load::class)->states('approved')->create([
            'customer_id'              => $customer3->id,
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW5 = factory(Load::class)->states('approved')->create([
            'customer_id'              => $customer4->id,
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $driver->blocked_list()->attach($customer1->id);
        $driver->blocked_list()->attach($customer3->id);

        $this->_createLicense($driver->id, $kw->id);
        $this->_createVisa($driver->id, $kw->id);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/driver/loads', ['current_country' => 'KW'], $header);

        $response->assertJson([
            'data' => [['id' => $loadKWKW3->id], ['id' => $loadKWKW5->id]]
        ]);

    }


    public function test_driver_can_only_see_loads_for_the_country_his_license_is_not_expired()
    {
        // get loads where origin is country id
        // get destination where id is in valid_visas

        $kw = $this->_createCountry('KW');
        $sa = $this->_createCountry('SA');
        $bh = $this->_createCountry('BH');
        $iq = $this->_createCountry('IQ');

        factory(CustomerLocation::class)->create(['country_id' => $kw->id]);
        factory(CustomerLocation::class)->create(['country_id' => $sa->id]);
        factory(CustomerLocation::class)->create(['country_id' => $bh->id]);
        factory(CustomerLocation::class)->create(['country_id' => $iq->id]);


        $loadKWKW1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW2 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWSA1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $sa->id,
        ]);

        $loadKWBH1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $bh->id,
        ]);

        $loadKWBH2 = factory(Load::class)->states('pending')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $bh->id,
        ]);

        $loadSABH1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $sa->id,
            'destination_location_id' => $bh->id,
        ]);

        $loadKWIQ1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $iq->id,
        ]);

        $loadBHIQ1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $bh->id,
            'destination_location_id' => $iq->id,
        ]);

        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $customer1 = $this->_createCustomer();

        $this->_createLicense($driver->id, $kw->id);
        $this->_createLicense($driver->id, $bh->id, false);
        $this->_createLicense($driver->id, $sa->id);
        $this->_createLicense($driver->id, $iq->id);

        $this->_createVisa($driver->id, $kw->id);
        $this->_createVisa($driver->id, $bh->id);
        $this->_createVisa($driver->id, $sa->id, false);
        $this->_createVisa($driver->id, $iq->id);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/driver/loads', ['current_country' => 'KW'], $header);

        $response->assertJson([
            'data' => [['id' => $loadKWKW1->id], ['id' => $loadKWKW2->id], ['id' => $loadKWIQ1->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id' => $loadKWBH1->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id' => $loadKWSA1->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id' => $loadKWBH2->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id' => $loadSABH1->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id' => $loadBHIQ1->id]]
        ]);
    }


    public function test_driver_can_only_see_loads_for_the_country_his_visa_is_not_expired()
    {
        // get loads where origin is country id
        // get destination where id is in valid_visas

        $kw = $this->_createCountry('KW');
        $sa = $this->_createCountry('SA');
        $bh = $this->_createCountry('BH');


        factory(CustomerLocation::class)->create(['country_id' => $kw->id]);
        factory(CustomerLocation::class)->create(['country_id' => $sa->id]);
        factory(CustomerLocation::class)->create(['country_id' => $bh->id]);

        $loadKWKW1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW2 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWSA1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $sa->id,
        ]);

        $loadKWBH1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $bh->id,
        ]);

        $loadKWBH2 = factory(Load::class)->states('pending')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $bh->id,
        ]);

        $loadSABH1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $sa->id,
            'destination_location_id' => $bh->id,
        ]);

        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $customer1 = $this->_createCustomer();

        $this->_createLicense($driver->id, $kw->id);
        $this->_createLicense($driver->id, $bh->id);
        $this->_createLicense($driver->id, $sa->id, false);

        $this->_createVisa($driver->id, $kw->id);
        $this->_createVisa($driver->id, $bh->id);
        $this->_createVisa($driver->id, $sa->id, false);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/driver/loads', ['current_country' => 'KW'], $header);

        $response->assertJson([
            'data' => [['id' => $loadKWKW1->id], ['id' => $loadKWKW2->id], ['id' => $loadKWBH1->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id' => $loadKWSA1->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id' => $loadKWBH2->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id' => $loadSABH1->id]]
        ]);

    }

    public function test_driver_gets_loads_with_trailer_id()
    {
        // get loads where origin is country id
        // get destination where id is in valid_visas

        $kw = $this->_createCountry('KW');
        factory(CustomerLocation::class)->create(['country_id' => $kw->id]);


        $loadKWKW1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'trailer_type_id'              => '1'
        ]);

        $loadKWKW2 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'trailer_type_id'              => '2'
        ]);

        $loadKWKW3 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'trailer_type_id'              => '1'
        ]);

        $loadKWKW4 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'trailer_type_id'              => '4'
        ]);

        $driver = $this->_createDriver();

        $this->_createVisa($driver->id, $kw->id);
        $this->_createLicense($driver->id, $kw->id);

        $customer1 = $this->_createCustomer();

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/driver/loads', ['current_country' => 'KW', 'trailer_type_id' => '1'], $header);

        $response->assertJson([
            'data' => [['id' => $loadKWKW1->id], ['id' => $loadKWKW3->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id' => $loadKWKW2->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id' => $loadKWKW4->id]]
        ]);

        //   check lwh

    }

    public function test_driver_gets_loads_with_valid_pass()
    {

        // return loads only which has no pass or pass which matches with driver

        $kw = $this->_createCountry('KW');
        factory(CustomerLocation::class)->create(['country_id' => $kw->id]);

        $loadKWKW1 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW2 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW3 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW4 = factory(Load::class)->states('approved')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $pass1 = factory(SecurityPass::class)->create(['country_id' => $kw->id]);
        $pass2 = factory(SecurityPass::class)->create(['country_id' => $kw->id]);
        $pass3 = factory(SecurityPass::class)->create(['country_id' => $kw->id]);

        $loadKWKW1->security_passes()->attach($pass1->id);

        $loadKWKW1->security_passes()->attach($pass2->id);
        $loadKWKW2->security_passes()->attach($pass2->id);

        $loadKWKW3->security_passes()->attach($pass3->id);

        $driver = $this->_createDriver();
        $driver->security_passes()->attach($pass2->id);

        $customer1 = $this->_createCustomer();

        // valid, 2

        $this->_createVisa($driver->id, $kw->id);
        $this->_createLicense($driver->id, $kw->id);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/driver/loads', ['current_country' => 'KW'], $header);

        $response->assertJson([
            'data' => [['id' => $loadKWKW1->id], ['id' => $loadKWKW2->id], ['id' => $loadKWKW4->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id' => $loadKWKW3->id]]
        ]);

    }


}
