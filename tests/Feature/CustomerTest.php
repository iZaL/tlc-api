<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Load;
use App\Models\SecurityPass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{

    use RefreshDatabase;

    public function test_customer_is_a_user()
    {
        $customer = factory(Customer::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);
        $this->assertInstanceOf(User::class, $customer->user);
    }

    public function test_customer_can_book_load()
    {
        $customer = $this->_createCustomer();
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);

        $loadData = $this->_createLoadPostData();
        $pass1 = factory(SecurityPass::class)->create();
        $pass2 = factory(SecurityPass::class)->create();
        $loadPasses = [
            'security_passes' => [$pass1->id, $pass2->id],
        ];

        $loadData = array_merge($loadData,$loadPasses);

        $this->assertDatabaseHas('security_passes',['id'=>$pass1->id]);
        $this->assertDatabaseHas('security_passes',['id'=>$pass2->id]);

        $response = $this->json('POST', '/api/customer/loads/', $loadData, $header);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
//                'load_status'    => 'pending'
            ]);

        $this->assertDatabaseHas('loads', array_merge(collect($loadData)->only(
            'id','trailer_type_id','packaging_id','origin_location_id','destination_location_id'
        )->toArray(),
            [
                'load_date' => Carbon::parse($loadData['load_date'])->toDateTimeString(),
                'unload_date' => Carbon::parse($loadData['unload_date'])->toDateTimeString(),
                'load_time_from' => Carbon::parse($loadData['load_time_from'])->toTimeString(),
                'load_time_to' => Carbon::parse($loadData['load_time_to'])->toTimeString(),
            ]));

    }

    public function test_load_has_status_pending_if_customer_cannot_book_direct()
    {
        $customer = $this->_createCustomer(['book_direct' => 0]);
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);
        $loadData = $this->_createLoadPostData();
        $others = ['status' => Load::STATUS_PENDING];
        $postData = array_merge($loadData, $others);
        $response = $this->json('POST', '/api/customer/loads', $loadData, $header);
        $this->assertDatabaseHas('loads', array_merge(collect($loadData)->only(
            'id','trailer_type_id','packaging_id','origin_location_id','destination_location_id'
        )->toArray(),
            [
                'status' => Load::STATUS_PENDING
            ]));

        $response->assertJson(['load_status' => 'pending']);
    }

    public function test_load_has_status_approved_if_customer_can_book_direct()
    {
        $customer = $this->_createCustomer(['book_direct' => 1]);
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);
        $loadData = $this->_createLoadPostData();
        $response = $this->json('POST', '/api/customer/loads', $loadData, $header);

        $this->assertDatabaseHas('loads', array_merge(collect($loadData)->only(
            'id','trailer_type_id','packaging_id','origin_location_id','destination_location_id'
        )->toArray(),
            [
                'status' => Load::STATUS_APPROVED
            ]));

        $response->assertJson(['load_status' => 'approved']);

    }

    /**
     * Status of Loads
     *
     * 1- Customer Books a Load, Status = Pending
     * 2- If Can Book Direct, Then Status = Waiting or Once Admin Approves, Status = Waiting
     * 3- If Admin Cancels, Status = Cancelled
     * 4- Driver Confirms the Booking, Status = Confirmed
     * 5- Driver Cancels the Booking, Status = Waiting
     * 6- Driver Starts The mission on Scheduled Date, Status = Started
     * 7- Driver Ends the mission successfully, Status = Completed
     */




}
