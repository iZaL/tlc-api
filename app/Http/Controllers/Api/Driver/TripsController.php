<?php


namespace App\Http\Controllers\Api\Driver;

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
use Validator;

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
            ->with(['booking.trailer_type','booking.origin','booking.destination'])
            ->whereHas('booking',function($q) use ($today) {
                $q
//                    ->whereDate('load_date','>=',$today)
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

    public function updateStatus($tripID, Request $request)
    {
        $driver = auth()->guard('api')->user()->driver;
        $trip = $this->tripModel->with(['booking'])->find($tripID);

        $tripManager = new TripManager($trip,$driver);

        try {
            switch ($request->status) {
                case 'accept' :
                    if($driver->is_legit) {
                        $tripManager->approveTrip();
                    } else {
                        $tripManager->acceptTrip();
                    }
                    break;
                case 'cancel' :
                    $tripManager->cancelTrip();
                    break;
                case 'start' :
                    $tripManager->startTrip();
                    break;
                case 'stop' :
                    $tripManager->stopTrip();
                    break;
                default:
                    break;
            }

        } catch (\Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()]);
        }

        $load = $trip->booking;
        $load->load('trip');

        return response()->json(['success'=>true,'data'=>new LoadResource($load)]);
    }

    public function saveDocuments($tripID, Request $request)
    {

        $validation = Validator::make($request->all(), [
            'document_type_id' => 'required',
            'uploads' => 'required|array',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $driver = auth()->guard('api')->user()->driver;

        $trip = $this->tripModel->with(['booking'])->find($tripID);

        $uploads = $request->uploads;

        $amount = $request->amount;
        $documentTypeID = $request->document_type_id;

        foreach ($uploads as $upload) {
            $trip->documents()->attach($documentTypeID,[
                'trip_id' => $trip->id,
                'amount' => $amount,
                'url' => $upload,
                'extension' => 'image',
            ]);
        }

        $load = $trip->booking;
        $load->load('trip.documents');

        return response()->json(['success'=>true,'data'=>new LoadResource($load)]);
    }

}