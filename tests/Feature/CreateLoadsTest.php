<?php

namespace Tests\Feature;

use App\Models\Shipper;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateLoadsTest extends TestCase
{

    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
    }

    public function testShipperBelongsToUser()
    {
        $shipper = factory(Shipper::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $this->assertInstanceOf(User::class, $shipper->user);
    }

    public function testShipperCanBookLoad()
    {
        $shipper = $this->_createShipper();
        $header = $this->_createHeader(['api_token' => $shipper->user->api_token]);
        $postData = $this->_createPostData();
        $response = $this->json('POST', '/api/load/book', $postData, $header);
        $this->assertDatabaseHas('loads', $postData);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'type' => 'created'
            ]);
    }
}
