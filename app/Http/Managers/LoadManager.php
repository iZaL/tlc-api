<?php

namespace App\Http\Managers;


use App\Exceptions\Driver\BusyOnScheduleException;
use App\Exceptions\Driver\DuplicateTripException;
use App\Exceptions\Driver\FleetsBookedException;
use App\Exceptions\Driver\ShipperBlockedException;
use App\Exceptions\Driver\TLCBlockedException;
use App\Exceptions\Load\LoadExpiredException;
use App\Exceptions\TripConfirmationFailedException;
use App\Models\Driver;
use App\Models\Load;
use App\Models\Trip;
use Carbon\Carbon;

class LoadManager
{
    /**
     * @var Load
     */
    private $load;

    /**
     * LoadManager constructor.
     * @param Load $load
     */
    public function __construct(Load $load)
    {
        $this->load = $load;
    }

    public function updateStatus()
    {

    }

}