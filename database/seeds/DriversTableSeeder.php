<?php

use App\Models\User;
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

        $pass1 = factory(\App\Models\SecurityPass::class)->create(['country_id' => $kw->id, 'name_en' =>'KOC', 'name_ar' =>'KOC']);
        $pass2 = factory(\App\Models\SecurityPass::class)->create(['country_id' => $kw->id, 'name_en' =>'KNPC', 'name_ar' =>'KNPC']);

        $routeKWSA = \App\Models\Route::where('origin_country_id',$kw->id)->where('destination_country_id',$sa->id)->first();
        $routeKWOM = \App\Models\Route::where('origin_country_id',$kw->id)->where('destination_country_id',$om->id)->first();

        $driver->security_passes()->sync([$pass1->id=>['image' => 'https://source.unsplash.com/800x400/?files'],$pass2->id=>['image' => 'https://source.unsplash.com/800x400/?files']]);

        //[ KW->Sa ],  [ KW->OM = KW->SA->AE->OM ]
        $driver->routes()->sync([$routeKWSA->id,$routeKWOM->id]);

        //licenses
        // KW, SA, AE, OM

        $driver->documents()->create(['image' => 'https://source.unsplash.com/800x400/?files','type'=>'nationality','country_id'=>$in->id,'expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString(),'number'=>str_random(10)]);
        $driver->documents()->create(['image' => 'https://source.unsplash.com/800x400/?files','type'=>'license','country_id'=>$kw->id,'expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString(),'number'=>str_random(10)]);
        $driver->documents()->create(['image' => 'https://source.unsplash.com/800x400/?files','type'=>'license','country_id'=>$sa->id,'expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString(),'number'=>str_random(10)]);
        $driver->documents()->create(['image' => 'https://source.unsplash.com/800x400/?files','type'=>'license','country_id'=>$ae->id,'expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString(),'number'=>str_random(10)]);
        $driver->documents()->create(['image' => 'https://source.unsplash.com/800x400/?files','type'=>'license','country_id'=>$om->id,'expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString(),'number'=>str_random(10)]);
        $driver->documents()->create(['image' => 'https://source.unsplash.com/800x400/?files','type'=>'visa','country_id'=>$kw->id,'expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString()]);
        $driver->documents()->create(['image' => 'https://source.unsplash.com/800x400/?files','type'=>'visa','country_id'=>$sa->id,'expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString()]);
        $driver->documents()->create(['image' => 'https://source.unsplash.com/800x400/?files','type'=>'visa','country_id'=>$ae->id,'expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString()]);
        $driver->documents()->create(['image' => 'https://source.unsplash.com/800x400/?files','type'=>'visa','country_id'=>$om->id,'expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString()]);
        $driver->documents()->create(['image' => 'https://source.unsplash.com/800x400/?files','type'=>'residency','country_id'=>$kw->id,'expiry_date' => \Carbon\Carbon::now()->addDays(rand(100,1000))->toDateString()]);

        // loads

        // create customer
        $customer = factory(\App\Models\Customer::class)->create();
        $customerOrigin= $customer->locations()->create(['country_id' => $kw->id,'type' => 'pick', 'latitude' => '29.3759', 'longitude' => '47.9774','city_en' => 'Jahra']);
        $customerDestination = $customer->locations()->create(['country_id' => $sa->id,'type' => 'drop', 'latitude' => '23.8859', 'longitude' => '45.0792','city_en' => 'Jeddah']);

        $load = factory(\App\Models\Load::class)->create([
            'customer_id' => $customer->id,
            'origin_location_id' => $customerOrigin->id,
            'destination_location_id' => $customerDestination->id,
            'status' => \App\Models\Load::STATUS_ENROUTE
        ]);

        $load1 = factory(\App\Models\Load::class)->create([
            'customer_id' => $customer->id,
            'origin_location_id' => $customerOrigin->id,
            'destination_location_id' => $customerDestination->id,
            'status' => \App\Models\Load::STATUS_PENDING
        ]);


        $trip1 = factory(\App\Models\Trip::class)->create(['driver_id'=>$driver->id,'status' => \App\Models\Trip::STATUS_ENROUTE,'load_id'=>$load->id]);
        $trip2 = factory(\App\Models\Trip::class)->create(['driver_id'=>$driver->id,'status' => \App\Models\Trip::STATUS_PENDING,'load_id'=>$load1->id]);

        $document = \App\Models\DocumentType::first();

        factory(\App\Models\TripDocument::class)->create(['trip_id'=>$trip1->id,'document_type_id' => $document->id]);


        /// drivers
        ///
        ///
//        factory(\App\Models\Driver::class,1)->create([
//            'offline' =>1,
//            'user_id' => function () {
//                return factory(User::class)->create(['name_en'=>'Ali','email'=>'ali@test.com'])->id;
//            },
//        ]);

//        factory(\App\Models\Driver::class,1)->create([
//            'offline' =>1,
//            'user_id' => function () {
//                return factory(User::class)->create(['name_en'=>'Abbas','email'=>'abbas@test.com'])->id;
//            },
//        ]);
//
//        factory(\App\Models\Driver::class,1)->create([
//            'offline' =>1,
//            'user_id' => function () {
//                return factory(User::class)->create(['name_en'=>'Mohammad','email'=>'mohammad@test.com'])->id;
//            },
//        ]);
//
//        factory(\App\Models\Driver::class,1)->create([
//            'user_id' => function () {
//                return factory(User::class)->create(['name_en'=>'Hussain','email'=>'hussain@test.com'])->id;
//            },
//        ]);
//
//        factory(\App\Models\Driver::class,1)->create([
//            'user_id' => function () {
//                return factory(User::class)->create(['name_en'=>'Nasser','email'=>'nasser@test.com'])->id;
//            },
//        ]);

    }
}
