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
        if ($bookingDate < $today) {
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
        if ($loadStatus > Trip::STATUS_REJECTED) {
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
     * @throws CustomerBlockedException
     */
    private function isDriverBlockedByCustomer()
    {
        $customerID = $this->trip->booking->customer_id;

        $driver = $this->driver;
        if ($driver->blocked_list->contains($customerID)) {
            throw new CustomerBlockedException('driver_blocked');
        }

        return false;
    }

    /**
     * @throws DuplicateTripException
     * check whether the driver has already booked on this trip, but only allow if he has a booking with
     * status of pending which is the default status.
     */
    private function hasDuplicateTrip()
    {
        $driver = $this->driver;
        $trip = $this->trip;
        $trips = $driver->trips
            ->where('load_id',$trip->booking->id)
            ->where('status','>=',$trip::STATUS_APPROVED)
            ->count()
        ;

        if ($trips > 0) {
            throw new DuplicateTripException('duplicate_trip');
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
            ->where('status', '>=', Trip::STATUS_CONFIRMED)
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
            ->whereDate('driver_blocked_dates.to', '>=', $loadDate)
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
        $this->isDriverBlockedByCustomer();
        $this->hasDuplicateTrip();
        $this->isLoadFleetsBooked();
        $this->driverHasAnotherTrip();
    }

    /**
     * @return bool
     * Can driver accept the trip
     */
    public function canAcceptTrip()
    {
        $trip = $this->trip;

        if($trip->status !== $trip::STATUS_PENDING) {
            return false;
        }

        try {
            $this->validateTrip();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     * Can driver cancel the trip
     */
    public function canCancelTrip()
    {
        $trip = $this->trip;
        if($trip->status >= $trip::STATUS_APPROVED && $trip->status < $trip::STATUS_CONFIRMED) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     * Can driver accept the trip
     */
    public function canConfirmTrip()
    {
        $trip = $this->trip;

        if($trip->status <= $trip::STATUS_APPROVED && $trip->status >= $trip::STATUS_REJECTED ) {
            return false;
        }

        try {
            $this->validateTrip();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Confirm the booking
     */
    public function confirmTrip()
    {
        $this->updateDriverBlockedDates();
        $this->updateTripStatus(Trip::STATUS_CONFIRMED);
        return true;
    }

    public function cancelTrip()
    {
        $this->updateTripStatus(Trip::STATUS_CANCELLED);
    }

    public function acceptTrip()
    {
        $this->updateTripStatus(Trip::STATUS_ACCEPTED);
    }

    public function approveTrip()
    {
        $this->updateTripStatus(Trip::STATUS_APPROVED);
    }

    public function startTrip()
    {
        $this->updateTripStatus(Trip::STATUS_DISPATCHED);
    }

    public function stopTrip()
    {
        $this->updateTripStatus(Trip::STATUS_OFFLOADED);
    }

    public function completeTrip()
    {
        $this->updateTripStatus(Trip::STATUS_COMPLETED);
    }


}