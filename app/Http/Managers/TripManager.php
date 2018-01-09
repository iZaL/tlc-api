<?php

namespace App\Http\Managers;


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

        $this->checkIsDriverBlocked();
        $this->checkHasShipperBlockedTheDriver();

        return $this;
    }

    /**
     * @return boolean
     * @throws TLCBlockedException
     */
    private function checkIsDriverBlocked()
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
    private function checkIsDriverBlockedByShipper()
    {
        $shipperID = $this->trip->booking->shipper_id;

        $driver = $this->driver;
        $driver->load('blocked_list');

        if($driver->blocked_list->contains($shipperID)) {
            throw new ShipperBlockedException(__('general.driver_blocked'));
        }

        return false;
    }
}