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

    use DatabaseTransactions;

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

        $shipper = factory(Shipper::class)->create([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            }
        ]);

        $headers['Authorization'] = 'Bearer ' . $shipper->user->api_token;

        $postData = [
            'shipper_id'              => '1',
            'origin_location_id'      => '1',
            'destination_location_id' => '1',
            'price'                   => rand(100, 1000),
            'distance'                => rand(100, 1000),
            'request_documents'       => '0',
            'request_pictures'        => '0',
            'fixed_rate'              => '1',
            'status'                  => 'busy',
            'scheduled_at'            => \Carbon\Carbon::now()->addDays(1, 10)->toDateTimeString()
        ];

        $response = $this->json('POST', '/api/load/book', $postData, $headers);

        $this->assertDatabaseHas('loads', $postData);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);;

    }
}
