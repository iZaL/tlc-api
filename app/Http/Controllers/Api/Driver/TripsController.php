<?php


namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Managers\LoadManager;
use App\Http\Managers\TripManager;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use App\Models\Load;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripsController extends Controller
{


    private $tripModel;
    private $loadModel;

    public function __construct(Load $loadModel,Trip $tripModel)
    {
        $this->loadModel = $loadModel;
        $this->tripModel = $tripModel;
    }

    public function getUpcomingTrips()
    {
        $driver = Auth::guard('api')->user()->driver;

        $today = Carbon::today()->toDateString();

        $trips = $this->tripModel
            ->with(['booking.trailer','booking.origin','booking.destination'])
            ->whereHas('booking',function($q) use ($today) {
                $q
                    ->whereDate('load_date','>=',$today)
                    ->orderBy('load_date','desc')
                ;
            })
            ->ofStatus('pending')
            ->get()
        ;

        $driver->upcoming_trips = TripResource::collection($trips);

        return response()->json(['success'=>true,'data'=>$driver]);
    }

    public function confirmTrip($tripID, Request $request)
    {
        $trip = $this->tripModel->with(['booking.trips'])->find($tripID);
        $driver = Auth::guard('api')->user()->driver;

        if(!$driver->available) {
            return response()->json(['success'=>false,'message' => __('general.driver_offline')]);
        }

        $tripManager = new TripManager($trip,$driver);

        try {
            $tripManager->validateTrip();
        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message' => $e->getMessage()]);
        }

        if($tripManager->confirmTrip()) {
            //@todo: send notifications

            //@todo: check whether all the fleets are booked, if yes, set load status respectively

            $load = $trip->booking;
            $loadManager = new LoadManager($load);
            $loadManager->updateStatus('confirmed');

            return response()->json(['success'=>true]);
        }

        return response()->json(['success'=>false,'message' => __('general.unknown_error')]);

    }
}