<?php

namespace App\Http\Managers;


use App\Exceptions\Driver\BusyOnScheduleException;
use App\Exceptions\Driver\DuplicateTripException;
use App\Exceptions\Driver\FleetsBookedException;
use App\Exceptions\Driver\LoadHasAlreadyConfirmed;
use App\Exceptions\Driver\ShipperBlockedException;
use App\Exceptions\Driver\TLCBlockedException;
use App\Exceptions\Load\LoadExpiredException;
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
     * @return bool
     * @throws LoadExpiredException
     */
    private function isLoadExpired()
    {
        $bookingDate = $this->trip->booking->load_date;
        $today = Carbon::today()->toDateString();
        if ($bookingDate <= $today) {
            throw new LoadExpiredException('load_expired');
        }
        return false;
    }

    /**
     * @return bool
     * @throws LoadHasAlreadyConfirmed
     */
    private function isAllowedToBook()
    {
        $loadStatus = $this->trip->booking->status;
        if ($loadStatus !== 'pending') {
            throw new LoadHasAlreadyConfirmed('load_already_confirmed');
        }
        return false;
    }

    /**
     * @return boolean
     * @throws TLCBlockedException
     */
    private function isDriverBlocked()
    {
        if ($this->driver->blocked) {
            throw new TLCBlockedException('driver_blocked');
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
        if ($driver->blocked_list->contains($shipperID)) {
            throw new ShipperBlockedException('driver_blocked');
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
        $hasTrips = $driver->trips->contains($this->trip->id);

        if ($hasTrips) {
            $oldTrips = $driver->trips->where('status','!=','pending')->count();

            if ($oldTrips > 0) {
                throw new DuplicateTripException('duplicate_trip');
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
        $fleetCount = $load->fleet_count;


        $loadTrips = $load->trips()
            ->where('status', 'confirmed')
            ->orWhere('status', 'working')
            ->orWhere('status', 'completed')
            ->count()
        ;

        if ($loadTrips >= $fleetCount) {
            throw new FleetsBookedException('fleet_bookings_full');
        }

        return false;
    }


    /**
     * @throws BusyOnScheduleException
     * Checks whether the driver's available date doesn't match the load date
     */
    private function driverHasAnotherTrip()
    {
        $driver = $this->driver;
        $loadDate = $this->trip->booking->load_date;

        $driverBlockedDates = $driver->blocked_dates()
            ->whereDate('driver_blocked_dates.from', '<=', $loadDate)
            ->where('driver_blocked_dates.to', '>=', $loadDate)
            ->count();

        if ($driverBlockedDates > 0) {
            throw new BusyOnScheduleException('driver_has_trip');
        }

        return false;
    }

    /**
     * @todo:figure out transit days
     */
    private function updateDriverBlockedDates()
    {
        $driver = $this->driver;
        $loadDate = $this->trip->booking->load_date;
        $returnDate = Carbon::parse($loadDate)->addDays(3)->toDateString();
        $driver->blocked_dates()->create(['from' => $loadDate, 'to' => $returnDate]);
    }

    private function updateTripStatus($status)
    {
        $trip = $this->trip;
        $trip->status = $status;
        $trip->save();
    }

    /**
     * Confirm the booking
     */
    public function confirmTrip()
    {
        $this->updateDriverBlockedDates();
        $this->updateTripStatus('confirmed');
        return true;
    }

    /**
     * @throws LoadExpiredException
     * @throws LoadHasAlreadyConfirmed
     */
    private function isValidLoad()
    {
        $this->isAllowedToBook();
        $this->isLoadExpired();
    }

    /**
     * @throws \Exception
     */
    public function validateTrip()
    {
        $this->isValidLoad();
        $this->isDriverBlocked();
        $this->isDriverBlockedByShipper();
        $this->hasDuplicateTrip();
        $this->isLoadFleetsBooked();
        $this->driverHasAnotherTrip();
    }

}