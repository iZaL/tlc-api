<?php

namespace App\Listeners;

use App\Events\LoadCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CalculateLoadDistance
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(LoadCreated $event)
    {

        $load = $event->load;

        $origin = $load->origin;
        $destination = $load->destination;
        $googleKey = env('GOOGLE_MAP_KEY');

        $geocode = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=$origin->latitude,$origin->longitude&destinations=$destination->latitude,$destination->longitude&mode=driving&sensor=false&key=$googleKey";

        $googleKey = env('GOOGLE_MAP_KEY');

        $origin = $load->origin;
        $destination = $load->destination;

        $geotools = new \League\Geotools\Geotools();
        $coordA   = new \League\Geotools\Coordinate\Coordinate([$origin->latitude,$origin->longitude]);
        $coordB   = new \League\Geotools\Coordinate\Coordinate([$destination->latitude,$destination->longitude]);

        $distance = $geotools->distance()->setFrom($coordA)->setTo($coordB);

        $load->trip_distance = round($distance->in('km')->haversine());
        $load->save();

        $json = file_get_contents($geocode);

        $duration = collect(json_decode($json,true))->get('rows')[0]['elements'][0]['duration']['value'];

        $load->trip_duration = $duration;
        $load->save();

        return $distance;

    }
}
