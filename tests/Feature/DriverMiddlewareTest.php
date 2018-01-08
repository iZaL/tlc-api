<?php

namespace Tests\Feature;

use App\Models\Shipper;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DriverMiddlewareTest extends TestCase
{

    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
    }

    public function testDriverMiddlewareReturnsFalseForInvalidUser()
    {
        $driver = $this->_createDriver(['active' => 0]);
        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('POST', '/api/driver/profile/update', [], $header);
        $response->assertStatus(403);
    }

    public function testShipperMiddlewareReturnsTrueForValidUser()
    {
        $driver = $this->_createDriver(['active' => 1]);
        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);
        $response = $this->json('POST', '/api/driver/profile/update', [], $header);
        $response->assertStatus(422);
    }
}
