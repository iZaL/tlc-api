<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
use App\Models\Employee;
use App\Models\Load;
use App\Models\Location;
use App\Models\Packaging;
use App\Models\Pass;
use App\Models\Customer;
use App\Models\CustomerLocation;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerLoadTest extends TestCase
{

    use RefreshDatabase;

    public function test_customer_can_create_load()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $trailer1 = factory(Trailer::class)->create();
        $packaging1 = factory(Packaging::class)->create();
        $originLocation = factory(CustomerLocation::class)->create();
        $destinationLocation = factory(CustomerLocation::class)->create();
        $loadDate = Carbon::now()->addDays(2)->toDateString();
        $loadTime = Carbon::now()->toTimeString();
        $receiverName = 'John';
        $receiverEmail = 'example@abc.com';
        $receiverMobile = 97978833;
        $receiverPhone = 97978833;
        $weight = '200';
        $pass1 = factory(Pass::class)->create();
        $pass2 = factory(Pass::class)->create();
        $pass3 = factory(Pass::class)->create();

        $loadPostData = [
            'trailer_id'              => $trailer1->id,
            'packaging_id'            => $packaging1->id,
            'origin_location_id'      => $originLocation->id,
            'destination_location_id' => $destinationLocation->id,
            'request_documents'       => 1,
            'use_own_truck'           => 1,
            'load_date'               => $loadDate,
            'load_time'               => $loadTime,
            'receiver_name'           => $receiverName,
            'receiver_email'          => $receiverEmail,
            'receiver_phone'          => $receiverPhone,
            'receiver_mobile'         => $receiverMobile,
            'weight' => $weight
        ];

        $loadPasses = [
            'passes' => [$pass1->id, $pass2->id, $pass3->id],
        ];

        $loadData = array_merge($loadPostData,$loadPasses);

        $response = $this->json('POST', '/api/customer/loads', $loadData, $header);

        $responseData = array_merge(['customer_id'=>$customer->id],$loadPostData);

        $this->assertDatabaseHas('loads',$responseData);

        $this->assertDatabaseHas('passes',['id'=>$pass1->id]);
        $this->assertDatabaseHas('passes',['id'=>$pass2->id]);
        $this->assertDatabaseHas('passes',['id'=>$pass3->id]);

        $response->assertJson(['success' => true]);

    }

    public function test_customer_gets_load_add_data()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $trailer1 = factory(Trailer::class)->create();
        $trailer2 = factory(Trailer::class)->create();

        $packaging1 = factory(Packaging::class)->create();
        $packaging2 = factory(Packaging::class)->create();

        $location = factory(CustomerLocation::class, 4)->create(['customer_id' => $customer->id]);

        $response = $this->json('GET', '/api/customer/loads/add/data', [], $header);

        $response->assertJson(['success' => true]);
        $response->assertJson(['data' => ['trailers' => [['id' => $trailer1->id], ['id' => $trailer2->id]]]]);
        $response->assertJson(['data' => ['packaging' => [['id' => $packaging1->id], ['id' => $packaging2->id]]]]);

    }

//
//    /**
//     * @todo
//     */
//
//    public function test_customer_needs_enough_credits_to_post_a_load()
//    {
//
//    }
//
//
//    public function test_trailer_selected_by_customer_can_load_the_cargo()
//    {
//        // does it violate the rules of destination and transit countries
//        // RULES : Dimensions, Load Capacity
//    }



}
