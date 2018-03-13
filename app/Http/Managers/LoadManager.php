<?php

namespace App\Http\Managers;


use App\Exceptions\Driver\BusyOnScheduleException;
use App\Exceptions\Driver\DuplicateTripException;
use App\Exceptions\Driver\FleetsBookedException;
use App\Exceptions\Driver\CustomerBlockedException;
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


    private function confirm()
    {
        // get no of fleet
        // if 1, direct approve

        $load = $this->load;

        $fleetCount = $load->fleet_count;

        if($fleetCount > 1) {
            $loadTrips = $load->trips()
                ->where('status', 'confirmed')
                ->orWhere('status', 'working')
                ->orWhere('status', 'completed')
                ->count()
            ;
        } else {
            $loadTrips = 1;
        }

        if($loadTrips == $fleetCount) {

            //@todo:  send confirm notifications

            $load->status = 'confirmed';
            $load->save();
        }

    }

    /**
     * @param $status
     * @return LoadManager
     */
    public function updateStatus($status)
    {
        switch ($status) {
            case 'confirmed':
                $this->confirm();
                break;
        }

        return $this;

    }

}