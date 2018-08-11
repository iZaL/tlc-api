<?php

namespace App\Jobs;

use Carbon\Carbon;
use Davibennun\LaravelPushNotification\Facades\PushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CalculateCordinateDistance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var array
     */
    private $deviceTokens;
    /**
     * @var string
     */
    private $message;


    private $args;
    /**
     * @var array
     */
    private $cordinates;

    /**
     * Create a new job instance.
     *
     * @param array $cordinates
     */
    public function __construct(array $cordinates)
    {
        $this->cordinates = $cordinates;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $coordinates = $this->cordinates;

        $latitude = $coordinates[0];
        $longitude = $coordinates[1];

        $geotools = new \League\Geotools\Geotools();
        $coordA   = new \League\Geotools\Coordinate\Coordinate([48.8234055, 2.3072664]);
        $coordB   = new \League\Geotools\Coordinate\Coordinate([43.296482, 5.36978]);

        $distance = $geotools->distance()->setFrom($coordA)->setTo($coordB);

        dd($distance);

//        $geotools   = new \League\;
//        dd($coordinate);

    }
}
