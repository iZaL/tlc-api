<?php

namespace Tests\Feature;

use App\Models\Shipper;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShipperMiddleware extends TestCase
{

    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
    }

    public function testShipperMiddlewareReturnsFalseForInvalidUser()
    {
        $shipper = $this->_createShipper(['active' => 0]);
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);
        $response = $this->json('GET', '/api/load/test', [], $header);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'type'    => 'invalid_request'
            ]);
    }

    public function testShipperMiddlewareReturnsTrueForValidUser()
    {
        $shipper = $this->_createShipper(['active' => 1]);
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);
        $response = $this->json('GET', '/api/load/test', [], $header);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
