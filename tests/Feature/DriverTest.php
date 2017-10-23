<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverVisas;
use App\Models\Load;
use App\Models\Location;
use App\Models\Pass;
use App\Models\Shipper;
use App\Models\Trailer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DriverTest extends TestCase
{

    use RefreshDatabase;

    public function test_driver_can_only_see_loads_for_the_country_his_visa_is_not_expired()
    {
        // get loads where origin is country id
        // get destination where id is in valid_visas

        $kw = $this->_createCountry('KW');
        $sa = $this->_createCountry('SA');
        $bh = $this->_createCountry('BH');

        factory(Location::class)->create(['country_id'=>$kw->id]);
        factory(Location::class)->create(['country_id'=>$sa->id]);
        factory(Location::class)->create(['country_id'=>$bh->id]);

        $loadKWKW1 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW2 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWSA1 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $sa->id,
        ]);

        $loadKWBH1 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $bh->id,
        ]);

        $loadKWBH2 = factory(Load::class)->states('pending')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $bh->id,
        ]);

        $loadSABH1 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $sa->id,
            'destination_location_id' => $bh->id,
        ]);

        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $visaKw = factory(DriverVisas::class)->create(
            [
                'driver_id'   => $driver->id,
                'country_id'  => $kw->id,
                'expiry_date' => Carbon::now()->addYear(1)->toDateString()
            ]);

        $visasa = factory(DriverVisas::class)->create(
            [
                'driver_id'   => $driver->id,
                'country_id'  => $sa->id,
                'expiry_date' => Carbon::now()->subYear(1)->toDateString()
            ]);

        $visabh = factory(DriverVisas::class)->create(
            [
                'driver_id'   => $driver->id,
                'country_id'  => $bh->id,
                'expiry_date' => Carbon::now()->addYear(1)->toDateString()
            ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/loads', ['current_country' => 'KW'], $header);

        $response->assertJson([
            'data' => [['id'=>$loadKWKW1->id],['id'=>$loadKWKW2->id],['id'=>$loadKWBH1->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id'=>$loadKWSA1->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id'=>$loadKWBH2->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id'=>$loadSABH1->id]]
        ]);

    }


    public function test_driver_gets_loads_with_trailer_id()
    {
        // get loads where origin is country id
        // get destination where id is in valid_visas

        $kw = $this->_createCountry('KW');
        factory(Location::class)->create(['country_id'=>$kw->id]);

        $loadKWKW1 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'trailer_id' => '1'
        ]);

        $loadKWKW2 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'trailer_id' => '2'
        ]);

        $loadKWKW3 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'trailer_id' => '1'
        ]);

        $loadKWKW4 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
            'trailer_id' => '4'
        ]);

        $driver = $this->_createDriver();

        $visaKw = factory(DriverVisas::class)->create(
            [
                'driver_id'   => $driver->id,
                'country_id'  => $kw->id,
                'expiry_date' => Carbon::now()->addYear(1)->toDateString()
            ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/loads', ['current_country' => 'KW','trailer_id' => '1'], $header);

        $response->assertJson([
            'data' => [['id'=>$loadKWKW1->id],['id'=>$loadKWKW3->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id'=>$loadKWKW2->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id'=>$loadKWKW4->id]]
        ]);

    }

    public function test_driver_gets_loads_with_valid_pass()
    {

        $kw = $this->_createCountry('KW');
        factory(Location::class)->create(['country_id'=>$kw->id]);

        $loadKWKW1 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW2 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW3 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $loadKWKW4 = factory(Load::class)->states('waiting')->create([
            'origin_location_id'      => $kw->id,
            'destination_location_id' => $kw->id,
        ]);

        $pass1 = factory(Pass::class)->create(['country_id'=>$kw->id]);
        $pass2 = factory(Pass::class)->create(['country_id'=>$kw->id]);
        $pass3 = factory(Pass::class)->create(['country_id'=>$kw->id]);

        $loadKWKW1->passes()->attach($pass1->id);

        $loadKWKW1->passes()->attach($pass2->id);
        $loadKWKW2->passes()->attach($pass2->id);

        $loadKWKW3->passes()->attach($pass3->id);

        $driver = $this->_createDriver();
        $driver->passes()->attach($pass2->id);

        // valid, 2

        $visaKw = factory(DriverVisas::class)->create(
            [
                'driver_id'   => $driver->id,
                'country_id'  => $kw->id,
                'expiry_date' => Carbon::now()->addYear(1)->toDateString()
            ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('GET', '/api/loads', ['current_country' => 'KW'], $header);

        $response->assertJson([
            'data' => [['id'=>$loadKWKW1->id],['id'=>$loadKWKW2->id],['id'=>$loadKWKW4->id]]
        ]);

        $response->assertJsonMissing([
            'data' => [['id'=>$loadKWKW3->id]]
        ]);

    }


}
