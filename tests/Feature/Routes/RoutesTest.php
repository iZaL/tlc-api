<?php

namespace Tests\Feature\Routes;

use App\Models\Country;
use App\Models\Driver;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class RoutesTestTest extends TestCase
{

    use RefreshDatabase;
    use WithoutMiddleware;
//

    public function test_driver_gets_transit_routes()
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
            'residence_country_id' => $kw->id
        ]);

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $kw = factory(Country::class)->create(['abbr'=>'KW','name_en'=>'Kuwait','name_ar'=>'Kuwait']);
        $sa = factory(Country::class)->create(['abbr'=>'SA','name_en'=>'Saudi Arabia','name_ar'=>'Saudi Arabia']);
        $om = factory(Country::class)->create(['abbr'=>'OM','name_en'=>'Oman','name_ar'=>'Oman']);
        $bh = factory(Country::class)->create(['abbr'=>'BH','name_en'=>'Bahrain','name_ar'=>'Bahrain']);
        $ae = factory(Country::class)->create(['abbr'=>'AE','name_en'=>'United Arab Emirates','name_ar'=>'United Arab Emirates']);
        $qa = factory(Country::class)->create(['abbr'=>'QA','name_en'=>'Qatar','name_ar'=>'Qatar']);
        $in = factory(Country::class)->create(['abbr'=>'IN','name_en'=>'India','name_ar'=>'India']);
        $iq = factory(Country::class)->create(['abbr'=>'IQ','name_en'=>'Iraq','name_ar'=>'Iraq']);


        $routeKWSA = factory(Route::class)->create(['origin_country_id'=>$kw->id,'destination_country_id'=>$sa->id]);
        $routeKWOM = factory(Route::class)->create(['origin_country_id'=>$kw->id,'destination_country_id'=>$om->id]);
        $routeKWBH = factory(Route::class)->create(['origin_country_id'=>$kw->id,'destination_country_id'=>$bh->id]);
        $routeKWAE = factory(Route::class)->create(['origin_country_id'=>$kw->id,'destination_country_id'=>$ae->id]);
        $routeKWQA = factory(Route::class)->create(['origin_country_id'=>$kw->id,'destination_country_id'=>$qa->id]);

        $routeSABH = factory(Route::class)->create(['origin_country_id'=>$sa->id,'destination_country_id'=>$bh->id]);
        $routeSAOM = factory(Route::class)->create(['origin_country_id'=>$sa->id,'destination_country_id'=>$om->id]);

        $routeKWBH->transits()->sync(['country_id'=>$sa->id]);
        $routeKWQA->transits()->sync(['country_id'=>$sa->id]);
        $routeKWAE->transits()->sync(['country_id'=>$sa->id]);

        $routeKWOM->transits()->sync([['country_id'=>$sa->id],['country_id'=>$ae->id,'order' => 2]]);

        $response = $this->json('GET', '/api/driver/routes/'.$routeKWOM->id.'/transits', [], $header);

        $response->assertJson(['success'=>true,'data'=>['id'=>$routeKWOM->id,'transits'=>[['id'=>$sa->id],['id'=>$ae->id]]]]);

    }

}
