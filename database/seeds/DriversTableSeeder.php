<?php

use Illuminate\Database\Seeder;

class DriversTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        factory(\App\Models\Driver::class,1)->create();
//        factory(\App\Models\DriverPass::class,5)->create();

        $user = \App\Models\User::where('email','driver@test.com')->first();
        $driver = $user->driver;
        $kw = \App\Models\Country::where('abbr','KW')->first();
        $sa = \App\Models\Country::where('abbr','SA')->first();
        $om = \App\Models\Country::where('abbr','OM')->first();
        $ae = \App\Models\Country::where('abbr','AE')->first();
        $in = \App\Models\Country::where('abbr','IN')->first();

        $pass1 = factory(\App\Models\Pass::class)->create(['country_id' => $kw->id,'name_en'=>'KOC','name_ar'=>'KOC']);
        $pass2 = factory(\App\Models\Pass::class)->create(['country_id' => $kw->id,'name_en'=>'KNPC','name_ar'=>'KNPC']);

        $driver->update([
            'residence_country_id'=>$kw->id,
            'nationality_country_id'=>$in->id
        ]);


//        ['origin_country_id'=>$kw->id,'destination_country_id'=>$sa->id]
        $routeKWSA = \App\Models\Route::where('origin_country_id',$kw->id)->where('destination_country_id',$sa->id)->first();
        $routeKWOM = \App\Models\Route::where('origin_country_id',$kw->id)->where('destination_country_id',$om->id)->first();

        $driver->passes()->sync([$pass1->id,$pass2->id]);

        //[ KW->Sa ],  [ KW->OM = KW->SA->AE->OM ]
        $driver->routes()->sync([$routeKWSA->id,$routeKWOM->id]);

        //licenses
        // KW, SA, AE, OM

        $driver->licenses()->attach($kw->id,['expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString(),'number'=>str_random(10)]);
        $driver->licenses()->attach($sa->id,['expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString(),'number'=>str_random(10)]);
        $driver->licenses()->attach($ae->id,['expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString(),'number'=>str_random(10)]);
        $driver->licenses()->attach($om->id,['expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString(),'number'=>str_random(10)]);

        //visas
        $driver->visas()->attach($kw->id,['expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString()]);
        $driver->visas()->attach($sa->id,['expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString()]);
        $driver->visas()->attach($ae->id,['expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString()]);
        $driver->visas()->attach($om->id,['expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString()]);

    }
}
