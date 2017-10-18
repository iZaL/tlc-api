<?php

namespace Tests;

use App\Models\Shipper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function _createUser()
    {
        return factory(User::class)->create();
    }

    public function _createPostData()
    {
        $postData = [
            'shipper_id'              => 1,
            'origin_location_id'      => 1,
            'destination_location_id' => 1,
            'price'                   => '200.00',
            'distance'                => '100',
            'request_documents'       => 0,
            'request_pictures'        => 0,
            'fixed_rate'              => 1,
            'status'                  => 'busy',
            'scheduled_at'            => '2017-10-19 11:15:25'
        ];
        return $postData;
    }

    public function _createHeader($array)
    {
        $headers['Authorization'] = 'Bearer ' . $array['api_token'];
        return $headers;
    }

    public function _createShipper($array = [])
    {
        $shipper = factory(Shipper::class)->create(array_merge([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            },
        ],$array));

        return $shipper;
    }

}
