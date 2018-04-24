<?php

namespace App\Managers;


use App\Models\Load;

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
            $loadTrips = $load->success_trips()
                ->count()
            ;
        } else {
            $loadTrips = 1;
        }

        if($loadTrips == $fleetCount) {

            //@todo:  send confirm notifications

            $load->status = Load::STATUS_CONFIRMED;
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
            case Load::STATUS_CONFIRMED:
                $this->confirm();
                break;
        }

        return $this;

    }


}