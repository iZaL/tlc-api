<?php

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kw = factory(\App\Models\Country::class)->create(['abbr'=>'KW','name_en'=>'Kuwait','name_ar'=>'Kuwait']);
        $sa = factory(\App\Models\Country::class)->create(['abbr'=>'SA','name_en'=>'Saudi Arabia','name_ar'=>'Saudi Arabia']);
        $om = factory(\App\Models\Country::class)->create(['abbr'=>'OM','name_en'=>'Oman','name_ar'=>'Oman']);
        $bh = factory(\App\Models\Country::class)->create(['abbr'=>'BH','name_en'=>'Bahrain','name_ar'=>'Bahrain']);
        $ae = factory(\App\Models\Country::class)->create(['abbr'=>'AE','name_en'=>'United Arab Emirates','name_ar'=>'United Arab Emirates']);
        $qa = factory(\App\Models\Country::class)->create(['abbr'=>'QA','name_en'=>'Qatar','name_ar'=>'Qatar']);
        $in = factory(\App\Models\Country::class)->create(['abbr'=>'IN','name_en'=>'India','name_ar'=>'India']);
        $iq = factory(\App\Models\Country::class)->create(['abbr'=>'IQ','name_en'=>'Iraq','name_ar'=>'Iraq']);
//        factory(\App\Models\Country::class, 1)->create(['name_en'=>'Iraq']);
//        factory(\App\Models\Country::class, 1)->create(['name_en'=>'India']);
//        factory(\App\Models\Country::class,5)->create();


        $routeKWSA = factory(\App\Models\Route::class)->create(['origin_country_id'=>$kw->id,'destination_country_id'=>$sa->id]);
        $routeKWOM = factory(\App\Models\Route::class)->create(['origin_country_id'=>$kw->id,'destination_country_id'=>$om->id]);
        $routeKWBH = factory(\App\Models\Route::class)->create(['origin_country_id'=>$kw->id,'destination_country_id'=>$bh->id]);
        $routeKWAE = factory(\App\Models\Route::class)->create(['origin_country_id'=>$kw->id,'destination_country_id'=>$ae->id]);
        $routeKWQA = factory(\App\Models\Route::class)->create(['origin_country_id'=>$kw->id,'destination_country_id'=>$qa->id]);

        $routeSABH = factory(\App\Models\Route::class)->create(['origin_country_id'=>$sa->id,'destination_country_id'=>$bh->id]);
        $routeSAOM = factory(\App\Models\Route::class)->create(['origin_country_id'=>$sa->id,'destination_country_id'=>$om->id]);


        $routeKWBH->transits()->sync(['country_id'=>$sa->id]);
        $routeKWQA->transits()->sync(['country_id'=>$sa->id]);
        $routeKWAE->transits()->sync(['country_id'=>$sa->id]);

        $routeKWOM->transits()->sync([['country_id'=>$sa->id],['country_id'=>$ae->id,'order' => 2]]);

        $countries = \App\Models\Country::all();

        foreach($countries as $country) {
            factory(\App\Models\Location::class,10)->create(['country_id'=>$country->id]);
        }

    }

}
