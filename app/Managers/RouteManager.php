<?php

namespace App\Managers;


use App\Exceptions\Driver\BusyOnScheduleException;
use App\Exceptions\Driver\DuplicateTripException;
use App\Exceptions\Driver\FleetsBookedException;
use App\Exceptions\Driver\LoadHasAlreadyConfirmed;
use App\Exceptions\Driver\CustomerBlockedException;
use App\Exceptions\Driver\TLCBlockedException;
use App\Exceptions\Load\LoadExpiredException;
use App\Exceptions\TripConfirmationFailedException;
use App\Models\Driver;
use App\Models\Route;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RouteManager
{
    /**
     * @var Route
     */
    private $route;

    /**
     * RouteManager constructor.
     */
    public function __construct()
    {
        $this->route = new Route();
    }

    /**
     * @param $originCountryID
     * @param $destinationCountryID
     * @return array|null
     */
    public function getRouteCountries($originCountryID, $destinationCountryID): array
    {
        $route = $this->getRoute($originCountryID, $destinationCountryID);
        if (!$route) {
            return null;
        }
        $transits = $route->transits->pluck('id')->toArray();
        return $transitRoutes = array_merge([$originCountryID, $destinationCountryID], $transits);
    }

    public function getRoute($originCountryID, $destinationCountryID)
    {
        $route = $this->route->with('transits')
            ->where('origin_country_id', $originCountryID)
            ->where('destination_country_id', $destinationCountryID)
            ->first();

        return $route;
    }

    public function getRouteDrivers($originCountryID, $destinationCountryID)
    {
        $drivers = DB::table('driver_routes as dr')
            ->join('routes', function ($join) {
                $join->on('dr.route_id', '=', 'routes.id')
                    ->where('dr.active', 1);
            })
            ->where('routes.origin_country_id', $originCountryID)
            ->where('routes.destination_country_id', $destinationCountryID)
            ->select('dr.driver_id')
            ->groupBy('dr.driver_id')
            ->pluck('dr.driver_id')
        ;
        return $drivers;
    }


}