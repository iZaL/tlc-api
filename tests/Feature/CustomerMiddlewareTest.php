<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerMiddlewareTest extends TestCase
{

    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
    }

    public function testCustomerMiddlewareReturnsFalseForInvalidUser()
    {
        $customer = $this->_createCustomer(['active' => 0]);
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);
        $response = $this->json('POST', '/api/customer/loads', [], $header);
        $response->assertStatus(403);
    }

    public function testCustomerMiddlewareReturnsTrueForValidUser()
    {
        $customer = $this->_createCustomer(['active' => 1]);
        $header = $this->_createHeader(['api_token' => $customer->user->api_token]);
        $response = $this->json('POST', '/api/customer/loads', [], $header);
        $response->assertStatus(422);
    }
}
