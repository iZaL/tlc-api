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

class RouteManager
{
    /**
     * @var Route
     */
    private $route;

    /**
     * RouteManager constructor.
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * @param $originCountryID
     * @param $destinationCountryID
     * @return array|null
     */
    public function getRouteCountries($originCountryID, $destinationCountryID) :array
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

}