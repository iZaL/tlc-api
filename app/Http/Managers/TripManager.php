<?php

namespace App\Http\Managers;


use App\Exceptions\Driver\BusyOnScheduleException;
use App\Exceptions\Driver\DuplicateTripException;
use App\Exceptions\Driver\FleetsBookedException;
use App\Exceptions\Driver\ShipperBlockedException;
use App\Exceptions\Driver\TLCBlockedException;
use App\Exceptions\TripConfirmationFailedException;
use App\Models\Driver;
use App\Models\Trip;
use Carbon\Carbon;

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
     * @return boolean
     */
    public function confirmTrip()
    {
        if($this->canBookTrip()) {
            $this->confirm();
            return true;
        }
        return false;
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

    private function canBookTrip()
    {
        $this->isDriverBlocked();
        $this->isDriverBlockedByShipper();
        $this->hasDuplicateTrip();
        return true;
    }

    /**
     * @throws BusyOnScheduleException
     */
    public function driverHasAnotherTrip()
    {
        $driver = $this->driver;
        $load = $this->trip->booking;
        $loadDate = $load->load_date; // ex:2018-01-12
        $driverAvailableDate = $driver->available_from; // ex: 2018-01-11

//        dd('2018-01-14' > '2018-01-12');
        if($driverAvailableDate >= $loadDate) {
            throw new BusyOnScheduleException('wa');
        }

        return false;
    }

//    /**
//     * Confirm the booking
//     */
//    public function confirm()
//    {
//        $this->update
//    }
}