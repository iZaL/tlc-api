<?php

namespace Tests;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
use App\Models\Load;
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

    public function _createLoadPostData($array = [])
    {
        $postData = [
            'shipper_id'              => 1,
            'trailer_id'              => 1,
            'origin_location_id'      => 1,
            'destination_location_id' => 1,
            'price'                   => '200.00',
            'distance'                => '100',
            'request_documents'       => 0,
            'request_pictures'        => 0,
            'fixed_rate'              => 1,
            'load_date'            => '2017-10-19'
        ];

        return array_merge($postData, $array);
    }

    public function _createHeader($array)
    {
        $headers['Authorization'] = 'Bearer ' . $array['api_token'];
        return $headers;
    }

    public function _createCountry($abbr, $array = [])
    {
        $country = array_merge($array, ['abbr' => $abbr]);
        return factory(Country::class)->create($country);
    }

    public function _createVisa($driverID, $countryID, $valid = true)
    {
        factory(DriverVisas::class)->create(
            [
                'driver_id'   => $driverID,
                'country_id'  => $countryID,
                'expiry_date' => $valid ? Carbon::now()->addYear(1)->toDateString() : Carbon::now()->subYear(1)->toDateString()
            ]);
    }

    public function _createLicense($driverID, $countryID, $valid = true)
    {
        factory(DriverLicense::class)->create(
            [
                'driver_id'   => $driverID,
                'country_id'  => $countryID,
                'expiry_date' => $valid ? Carbon::now()->addYear(1)->toDateString() : Carbon::now()->subYear(1)->toDateString()
            ]);
    }

    public function _createShipper($array = [])
    {
        $shipper = factory(Shipper::class)->create(array_merge([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            },
        ], $array));

        return $shipper;
    }

    public function _createDriver($array = [])
    {
        $driver = factory(Driver::class)->create(array_merge([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            },
        ], $array));

        return $driver;
    }

    public function _createLoad($array = [])
    {
        $load = factory(Load::class)->create(array_merge([
            'shipper_id'              => 1,
            'trailer_id'              => 1,
            'origin_location_id'      => 1,
            'destination_location_id' => 1
        ], $array));

        return $load;
    }

}
