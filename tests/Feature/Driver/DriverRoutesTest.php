<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
use App\Models\Load;
use App\Models\Location;
use App\Models\Pass;
use App\Models\Customer;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DriverRoutesTestTest extends TestCase
{

    use RefreshDatabase;
    use WithoutMiddleware;
//

    public function test_available_routes_for_driver()
    {

        $kw = $this->_createCountry('KW');
        $sa = $this->_createCountry('SA');
        $qa = $this->_createCountry('QA');
        $om = $this->_createCountry('OM');
        $bh = $this->_createCountry('BH');

        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            },
//            'residence_country_id' => $kw->id
        ]);

        $truck = $this->_createTruck($kw->id);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $routeKWSA = factory(\App\Models\Route::class)->create(['origin_country_id' => $kw, 'destination_country_id' => $sa]);
        $routeKWQA = factory(\App\Models\Route::class)->create(['origin_country_id' => $kw, 'destination_country_id' => $qa]);
        $routeKWOM = factory(\App\Models\Route::class)->create(['origin_country_id' => $kw, 'destination_country_id' => $om]);
        $routeOMSA = factory(\App\Models\Route::class)->create(['origin_country_id' => $om, 'destination_country_id' => $sa]);
        $routeQASA = factory(\App\Models\Route::class)->create(['origin_country_id' => $qa, 'destination_country_id' => $sa]);
        $routeOMKW = factory(\App\Models\Route::class)->create(['origin_country_id' => $om, 'destination_country_id' => $kw]);
        $routeBHKW = factory(\App\Models\Route::class)->create(['origin_country_id' => $bh, 'destination_country_id' => $kw]);

        $routeKWSA->drivers()->save($driver);
        $routeKWQA->drivers()->save($driver);
        $routeOMSA->drivers()->save($driver);
        $routeQASA->drivers()->save($driver);
        $routeOMKW->drivers()->save($driver);
        $routeBHKW->drivers()->save($driver);

        $driverLoadingCountries = $driver->truck->registration_country->loading_routes;

        $this->assertEquals([$routeKWSA->id,$routeKWQA->id,$routeKWOM->id],$driverLoadingCountries->pluck('id')->toArray());

    }

    public function test_driver_can_add_route()
    {

        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            },
//            'truck_id' => $truck->id
        ]);

        $kw = $this->_createCountry('KW');

        $truck = $this->_createTruck($kw->id);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $body = [
            'route_id' => 1
        ];

        $response = $this->json('POST', '/api/driver/routes', $body, $header);

        $response->assertJson(['success'=>true]);

        $this->assertDatabaseHas('driver_routes',array_merge($body,['driver_id'=>$driver->id]));

    }

    public function test_driver_gets_routes()
    {
        $kw = $this->_createCountry('KW');
        $sa = $this->_createCountry('SA');
        $qa = $this->_createCountry('QA');
        $om = $this->_createCountry('OM');

        $driver = factory(Driver::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            },
//            'residence_country_id' => $kw->id
        ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $routeKWSA = factory(\App\Models\Route::class)->create(['origin_country_id' => $kw, 'destination_country_id' => $sa]);
        $routeKWQA = factory(\App\Models\Route::class)->create(['origin_country_id' => $kw, 'destination_country_id' => $qa]);
        $routeOMSA = factory(\App\Models\Route::class)->create(['origin_country_id' => $om, 'destination_country_id' => $sa]);

        $routeKWSA->drivers()->save($driver);
        $routeKWQA->drivers()->save($driver);
        $routeOMSA->drivers()->save($driver);

        $response = $this->json('GET', '/api/driver/routes', [], $header);

        $response->assertJson(['success'=>true,'data'=>['id'=>$driver->id,'routes'=>[['id'=>$routeKWSA->id],['id'=>$routeKWQA->id]]]]);

    }

}
