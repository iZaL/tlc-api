<?php

namespace App\Http\Managers;


use App\Exceptions\Driver\DuplicateTripException;
use App\Exceptions\Driver\FleetsBookedException;
use App\Exceptions\Driver\ShipperBlockedException;
use App\Exceptions\Driver\TLCBlockedException;
use App\Exceptions\TripConfirmationFailedException;
use App\Models\Driver;
use App\Models\Trip;

class TripManager
{
    /**
     * @var Trip
     */
    private $trip;
    /**
     * @var Driver
     */
    private $driver;

    /**
     * TripManager constructor.
     * @param Trip $trip
     * @param Driver $driver
     */
    public function __construct(Trip $trip, Driver $driver)
    {
        $this->trip = $trip;
        $this->driver = $driver;
    }

    /**
     * @return $this
     * @throws TLCBlockedException
     */
    public function confirmTrip()
    {
        $driver = $this->driver;
        $this->isDriverBlocked();
        $this->isDriverBlockedByShipper();
        $this->hasDuplicateTrip();
        return $this;
    }

    /**
     * @return boolean
     * @throws TLCBlockedException
     */
    private function isDriverBlocked()
    {
        if($this->driver->blocked) {
            throw new TLCBlockedException(__('general.driver_blocked'));
        }
        return false;
    }

    /**
     * @return boolean
     * @throws ShipperBlockedException
     */
    private function isDriverBlockedByShipper()
    {
        $shipperID = $this->trip->booking->shipper_id;

        $driver = $this->driver;
        $driver->load('blocked_list');

        if($driver->blocked_list->contains($shipperID)) {
            throw new ShipperBlockedException(__('general.driver_blocked'));
        }

        return false;
    }

    /**
     * @return boolean
     * @throws DuplicateTripException
     * check whether the driver has already booked on this trip, but only allow if he has a booking with
     * status of pending which is the default status.
     */
    private function hasDuplicateTrip()
    {
        $driver = $this->driver;
        $driver->load('trips');
        $hasTrips = $driver->trips->contains($this->trip->id);

        if($hasTrips) {
            $oldTrips = $driver->trips->where('status','!=','pending')->count();

            if($oldTrips > 0) {
                throw new DuplicateTripException(__('general.duplicate_trip'));
            }
        }

        return false;
    }

    /**
     * @throws FleetsBookedException
     * Check whether the fleets for the load is already booked
     */
    private function isLoadFleetsBooked()
    {
        $load = $this->trip->booking;
        $loadFleets = $load->fleet_count;

        $loadTrips = $load->trips
            ->where('status','!=','pending')
            ->where('status','!=','rejected')
            ->count();

        if($loadTrips >= $loadFleets) {
            throw new FleetsBookedException(__('general.fleet_bookings_full'));
        }

        return false;

    }

}