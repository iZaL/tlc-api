<?php

namespace Tests\Feature\Auth;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
use App\Models\Load;
use App\Models\Location;
use App\Models\Pass;
use App\Models\Shipper;
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

class LoginTest extends TestCase
{

    use RefreshDatabase;

    use WithoutMiddleware;

    public function test_login()
    {

        $user = $this->_createUser();
        $response = $this->json('POST', '/api/auth/login',['email'=>$user->email,'password'=>'password']);

        $response->assertJson(['success'=>true,'data'=>['id'=>$user->id],'meta'=>['api_token'=>$user->api_token]]);

    }

    public function test_driver_login()
    {

        $driver = $this->_createDriver();
        $response = $this->json('POST', '/api/auth/login',['email'=>$driver->user->email,'password'=>'password']);

        $response->assertJson(['success'=>true,'data'=>['id'=>$driver->user->id,'profile'=>[['id'=>$driver->id]]],'meta'=>['api_token'=>$driver->user->api_token]]);

    }


}
