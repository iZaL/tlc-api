<?php

namespace App\Managers;


use App\Exceptions\Driver\BusyOnScheduleException;
use App\Exceptions\Driver\DuplicateTripException;
use App\Exceptions\Driver\FleetsBookedException;
use App\Exceptions\Driver\CustomerBlockedException;
use App\Exceptions\Driver\TLCBlockedException;
use App\Exceptions\Load\LoadExpiredException;
use App\Exceptions\TripConfirmationFailedException;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use App\Models\Load;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DriverManager
{
    /**
     * @var Driver
     */
    private $driverModel;

    /**
     * DriverManager constructor.
     */
    public function __construct()
    {
        $this->driverModel = new Driver();
    }

//    public function getLoads(Request $request)
//    {
//        $validation = Validator::make($request->all(), [
//            'current_country' => 'required',
//        ]);
//
//        $driver = Auth::guard('api')->user()->driver;
//
//        $currentCountry = $this->countryModel->where('abbr', $request->current_country)->first();
//        $trailerID = $request->trailer_id;
//
//        $driverValidVisaCountries = $driver->valid_visas->pluck('id');
//        $driverValidLicenses = $driver->valid_licenses->pluck('id');
//        $blockedCustomers = $driver->blocked_list->pluck('id');
//        $driverValidPasses = $driver->passes->pluck('id');
//
//        $validCountries = $driverValidVisaCountries->intersect($driverValidLicenses);
//
//        $loads =
//            DB::table('loads')
//                ->join('customer_locations as sl', 'loads.origin_location_id', 'sl.id')
//                ->join('customers as s', 'loads.customer_id', 's.id')
//                ->leftJoin('load_passes as lp', 'loads.id', 'lp.load_id')
//                ->leftJoin('drivers as d', 'd.customer_id', 's.id')
//                ->when($trailerID, function ($q) use ($trailerID) {
//                    $q->where('trailer_id', $trailerID);
//                })
//                ->where('loads.status', 'waiting')
//                ->where(function ($query) use ($driverValidPasses) {
//                    $query
//                        ->whereIn('lp.pass_id', $driverValidPasses)
//                        ->orWhere('lp.pass_id', null);
//                })
//                ->where(function ($query) use ($driver) {
//                    $query
//                        ->where('d.id', $driver->id)
//                        ->orWhere('loads.use_own_truck', 0);
//                })
//                ->where('loads.origin_location_id', $currentCountry->id)
//                ->whereIn('loads.destination_location_id', $validCountries)
//                ->whereNotIn('loads.customer_id', $blockedCustomers)
//                ->select('loads.*')
//                ->paginate(20);
//
//        return new LoadResourceCollection($loads);
//    }

    /** get drivers
     * who are active
     * who are not offline
     * who are not blocked by customer
     * who are not blocked by tlc
     * who are not on other trips
     * who has valid visas (not expired) to destination country and transit country
     * who has valid licenses (not expired)
     * who has valid truck, trailer (length,width,height,capacity) depending on the load dimension
     * who has truck registered on same country as load origin country
     * who has added the load route in their route list
     * who has valid gate passes to the load destination if required
     * who works for same customer if customer prefers their own driver
     */
    public function getValidDrivers()
    {
        $drivers = DB::table('drivers')
            ->where('drivers.active', 1)
            ->where('drivers.offline', 0)
            ->where('drivers.blocked', 0)
            ->select('drivers.id')
            ->pluck('id');
        return $drivers;
    }

    public function getDriversWhoHasTrips($loadDate)
    {
        $drivers = DB::table('drivers')
            ->join('driver_blocked_dates as dbt', function ($join) use ($loadDate) {
                $join->on('drivers.id', '=', 'dbt.driver_id')
                    ->whereDate('dbt.from', '<=', $loadDate)
                    ->whereDate('dbt.to', '>=', $loadDate);
            })
            ->select('drivers.id')
            ->pluck('id');

        return $drivers;
    }

    public function getDriversWhoAreBlockedByCustomer($customerID)
    {
        $drivers = DB::table('drivers')
            ->join('blocked_drivers as bd', function ($join) use ($customerID) {
                $join->on('drivers.id', '=', 'bd.driver_id')
                    ->where('bd.customer_id', '=', $customerID);
            })
            ->select('drivers.id')
            ->pluck('id');

        return $drivers;
    }

    public function getDriversWhoHasValidVisas($countries = [], $loadDate)
    {
        $drivers = DB::table('driver_visas as dv')
            ->join('drivers', function ($join) use ($countries) {
                $join->on('drivers.id', '=', 'dv.driver_id');
            })
            ->whereIn('dv.country_id', $countries)
            ->having(DB::raw('count(*)'), '=', count($countries))
            ->whereDate('dv.expiry_date', '>', $loadDate)
            ->select('dv.driver_id')
            ->groupBy('dv.driver_id')
            ->pluck('dv.driver_id');

        return $drivers;
    }

    public function getDriversWhoHasValidLicenses($countries = [], $loadDate)
    {
        $drivers = DB::table('driver_licenses as dl')
            ->join('drivers', function ($join) use ($countries) {
                $join->on('drivers.id', '=', 'dl.driver_id');
            })
            ->whereIn('dl.country_id', $countries)
            ->having(DB::raw('count(*)'), '=', count($countries))
            ->whereDate('dl.expiry_date', '>', $loadDate)
            ->select('dl.driver_id')
            ->groupBy('dl.driver_id')
            ->pluck('dl.driver_id');

        return $drivers;
    }

    public function getDriversWhoHasValidPasses($passes = [])
    {
        $drivers = DB::table('driver_passes as dp')
            ->join('passes', function ($join) use ($passes) {
                $join->on('passes.id', '=', 'dp.pass_id');
            })
            ->whereIn('passes.id', $passes)
            ->having(DB::raw('count(*)'), '=', count($passes))
            ->select('dp.driver_id')
            ->groupBy('dp.driver_id')
            ->pluck('dp.driver_id');

        return $drivers;
    }


}