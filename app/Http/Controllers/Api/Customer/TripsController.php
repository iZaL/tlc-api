<?php


namespace App\Http\Controllers\Api\Customer;

use App\Events\DriversLocationUpdated;
use App\Events\DriverStartedJob;
use App\Http\Controllers\Controller;
use App\Managers\LoadManager;
use App\Managers\TripManager;
use App\Http\Resources\LoadResource;
use App\Http\Resources\TripResource;
use App\Jobs\SendPushNotificationsToAllDevice;
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

    public function getTripDetails($id)
    {
        $trip = $this->tripModel
            ->has('driver.truck.model')
            ->has('driver.truck.trailer.type')
            ->has('driver.truck.registration_country')
            ->with([
            'booking',
            'driver.user',
            'driver.truck.trailer.type',
            'driver.truck.model',
            'driver.truck.registration_country',
            'driver.nationalities',
        ])->find($id);

        if($trip) {
            return response()->json(['success'=>true,'load' => new LoadResource($trip->booking),'trip' => new TripResource($trip)]);
        }

        return response()->json(['success'=>false,'message'=>'unknown trip']);

    }

    public function getUpcomingTrips()
    {
        $driver = Auth::guard('api')->user()->driver;

        $today = Carbon::today()->toDateString();

        $trips = $this->tripModel
            ->with(['booking.trailer_type','booking.origin','booking.destination'])
            ->whereHas('booking',function($q) use ($today) {
                $q
                    ->whereDate('load_date','>=',$today)
                    ->orderBy('load_date','desc')
                ;
            })
            ->ofStatus(Trip::STATUS_PENDING)
            ->get()
        ;

        $driver->upcoming_trips = TripResource::collection($trips);

        return response()->json(['success'=>true,'data'=>$driver]);
    }

    public function confirmTrip($tripID, Request $request)
    {
        $trip = $this->tripModel->with(['booking.trips'])->find($tripID);
        $driver = Auth::guard('api')->user()->driver;

        if($driver->offline) {
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
            $loadManager->updateStatus(Load::STATUS_CONFIRMED);

            return response()->json(['success'=>true]);
        }

        return response()->json(['success'=>false,'message' => __('general.unknown_error')]);

    }

    public function startJob($id)
    {
        $trip = $this->tripModel->with(['load.user'])->find($id);
//        $trip->startJob();

//        $customer = $trip->load->user;

//        $pushTokens = $this->pushTokenModel->where('user_id',$customer->id)->pluck('token')->toArray();

//        $pushTokens = ['714dbc9d4ea47c1896651efedaa3c208ae5735bf3c426a40b3c71499112da6db'];

//        event(new DriverStartedJob($trip));

//        $trip = (new SendPushNotificationsToAllDevice($pushTokens,'Job Started'));

//        $this->dispatch($trip);

        $load = $trip->booking;

        return response()->json(['success'=>true,'data'=> new LoadResource($load)]);
    }

    public function finishJob($id)
    {
        $trip = $this->tripModel->with(['booking'])->find($id);
//        $trip->completeJob();

//        event(new DriverStartedJob($trip));

        $load = $trip->booking;

        return response()->json(['success'=>true,'data'=> new LoadResource($load)]);
    }

    public function updateLocation($tripID,Request $request)
    {
        $coords = $request->location['coords'];
        $payload = [
            'latitude' => $coords['latitude'],
            'longitude' => $coords['longitude'],
            'heading' => $coords['heading']
        ];

        event(new DriversLocationUpdated($tripID,$payload));
        return response()->json(['success'=>true,'data'=>$payload]);
    }
}