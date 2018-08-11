<?php

namespace Tests\Feature\Driver;

use App\Models\Load;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverUploadsTripDocumentTest extends TestCase
{

    use RefreshDatabase;


    public function test_driver_uploads_trip_documents()
    {
        $driver = $this->_createDriver();

        $header = $this->_createHeader(['api_token' => $driver->user->api_token]);

        $validLoad = $this->_createLoad([
            'load_date' => Carbon::now()->addDays(1)->toDateString(),
        ]);


        $validTrip = $validLoad->trips()->create(['driver_id' => $driver->id]);

        $payload = [
          'uploads' => ['http://fake.com/image1.png','http://fake.com/image2.png'],
          'document_type_id' => 1,
          'trip_id' => $validTrip->id,
        ];

        $response = $this->json('POST', '/api/driver/trips/'.$validTrip->id.'/documents/save', $payload, $header);

        dd($response);
    }
}
