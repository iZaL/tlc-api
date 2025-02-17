<?php

namespace Tests;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverDocument;
use App\Models\Load;
use App\Models\Customer;
use App\Models\Route;
use App\Models\Trailer;
use App\Models\TrailerType;
use App\Models\Trip;
use App\Models\Truck;
use App\Models\TruckMake;
use App\Models\TruckModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use ReflectionClass;

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
            'trailer_type_id'         => 1,
            'packaging_id'            => 1,
            'origin_location_id'      => 1,
            'destination_location_id' => 1,
            'request_documents'       => 0,
            'request_pictures'        => 0,
            'fixed_rate'              => 1,
            'load_date'               => '2017-10-19',
            'unload_date'               => '2017-10-22',
            'load_time_from'               => '2017-10-19 10:00:00',
            'unload_time_from'               => '2017-10-19 10:00:00',
            'load_time_to'               => '2017-10-19 10:00:00',
            'unload_time_to'               => '2017-10-19 10:00:00',
            'receiver_name'           => 'zal',
            'receiver_email'          => 'z4ls@live.com',
            'receiver_phone'          => '9992192299',
            'receiver_mobile'         => '9992192299',
            'weight'                  => 100,
            'security_passes' => [1,2],
            'trailer_quantity' => 1
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

    public function _createRoute($origin, $destination, $array = [])
    {
        $route = factory(Route::class)->create(['origin_country_id' => $origin->id, 'destination_country_id' => $destination->id]);

        if (array_key_exists('transit1', $array)) {
            $route->transits()->syncWithoutDetaching(['country_id' => $array['transit1']]);
        }

        if (array_key_exists('transit2', $array)) {
            $route->transits()->syncWithoutDetaching(['country_id' => $array['transit2']]);
        }

        return $route;

    }

    public function _createVisa($driverID, $countryID, $valid = true)
    {
        factory(DriverDocument::class)->create(
            [
                'type'        => 'visa',
                'driver_id'   => $driverID,
                'country_id'  => $countryID,
                'expiry_date' => $valid ? Carbon::now()->addYear(1)->toDateString() : Carbon::now()->subYear(1)->toDateString()
            ]);
    }

    public function _createLicense($driverID, $countryID, $valid = true)
    {
        factory(DriverDocument::class)->create(
            [
                'type'        => 'license',
                'driver_id'   => $driverID,
                'country_id'  => $countryID,
                'expiry_date' => $valid ? Carbon::now()->addYear(1)->toDateString() : Carbon::now()->subYear(1)->toDateString()
            ]);
    }

    public function _createTruck($countryID, $array = [])
    {

        $truck = factory(Truck::class)->create(array_merge([
            'model_id'                => function () {
                return factory(TruckModel::class)->create()->id;
            },
            'registration_country_id' => $countryID
        ], $array));

        return $truck;
    }

    public function _createCustomer($array = [])
    {
        $customer = factory(Customer::class)->create(array_merge([
            'user_id' => function () {
                return factory(User::class)->create()->id;
            },
        ], $array));

        return $customer;
    }

    public function _createDriver($array = [], $relations = [])
    {
        $driver = factory(Driver::class)->create(array_merge([
            'user_id'  => function () {
                return factory(User::class)->create()->id;
            },
            'truck_id' => array_key_exists('truck', $relations) ? function () {
                return factory(Truck::class)->create([
                    'trailer_id' => function () {
                        return factory(Trailer::class)->create([
                            'type_id' => function () {
                                return factory(TrailerType::class)->create()->id;
                            }
                        ])->id;
                    },
                    'model_id' => function() {
                        return factory(TruckModel::class)->create([
                            'make_id' => function () {
                                return factory(TruckMake::class)->create()->id;
                            }
                        ])->id;
                    },
                    'registration_country_id' => function() {
                        return factory(Country::class)->create()->id;
                    },
                ]);
            } : 1

        ], $array));

        return $driver;
    }

    public function _createLoad($array = [])
    {
        $load = factory(Load::class)->create(array_merge([
            'customer_id'             => 1,
            'trailer_type_id'         => 1,
            'origin_location_id'      => 1,
            'destination_location_id' => 1
        ], $array));

        return $load;
    }

    public function _createTrip($array = [])
    {
        $load = factory(Trip::class)->create(array_merge([
            'driver_id' => 1
        ], $array));

        return $load;
    }


    public function _makeDriverBusy($driver, $array = [])
    {
        $bookedFrom = Carbon::now()->addDays(3)->toDateString();
        $bookedUntil = Carbon::now()->addDays(6)->toDateString();
        $driver->blocked_dates()->create([
            'trip_id' => 1,
            'from' => isset($array['from']) ? $array['from']: $bookedFrom,
            'to' => isset($array['to']) ? $array['to']: $bookedUntil
        ]);
    }

}
