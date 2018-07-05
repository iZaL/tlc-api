<?php


namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Http\Resources\LoadResourceCollection;
use App\Http\Resources\UserResource;
use App\Models\Country;
use App\Models\Load;
use App\Models\Customer;
use App\Models\Trailer;
use App\Models\TrailerMake;
use App\Models\Truck;
use App\Models\TruckMake;
use App\Models\TruckModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrucksController extends Controller
{
    /**
     * @var TruckMake
     */
    private $truckMake;
    /**
     * @var TruckModel
     */
    private $truckModel;
    /**
     * @var Truck
     */
    private $truck;
    /**
     * @var TrailerMake
     */
    private $trailerMake;
    /**
     * @var Trailer
     */
    private $trailer;

    /**
     * TrucksController constructor.
     * @param TruckMake $truckMake
     * @param TruckModel $truckModel
     * @param Truck $truck
     * @param TrailerMake $trailerMake
     * @param Trailer $trailer
     */
    public function __construct(TruckMake $truckMake, TruckModel $truckModel, Truck $truck, TrailerMake $trailerMake,Trailer $trailer)
    {
        $this->truckMake = $truckMake;
        $this->truckModel = $truckModel;
        $this->truck = $truck;
        $this->trailerMake = $trailerMake;
        $this->trailer = $trailer;
    }

    public function saveTruck(Request $request)
    {
        $user = Auth::guard('api')->user();
        $driver = $user->driver;

        $validation = Validator::make($request->all(), [
//            'model_id' => 'required'
//            'make_id'  => 'required',
//            'model_id' => 'required',
//            'plate_number' => 'required',
//            'registration_number' => 'required',
//            'registration_expiry' => 'required',
//            'max_weight' => 'required',
//            'year' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $params = [];

        if($request->registration_expiry_date) {
            $params['registration_expiry_date'] = Carbon::parse($request->registration_expiry_date)->toDateString();
        }

        $keys = [
            'model_id',
            'registration_country_id','registration_number','registration_image',
            'plate_number','max_weight','image','year'
        ];

        if ($truck = $driver->truck) {
            $driver->truck->update(array_merge($request->only($keys), $params));
        } else {
            $params['driver_id'] = $driver->id;
            $this->truck->create(array_merge($request->only($keys), $params));
        }

        $driver->load('truck.trailer', 'truck.model','truck.registration_country');

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);

    }

    public function saveTrailer(Request $request)
    {
        $user = Auth::guard('api')->user();
        $driver = $user->driver;

        $validation = Validator::make($request->all(), [
            'truck_id' => 'required',
            'make_id'  => 'required',
            'type_id' => 'required',
//            'max_weight' => 'required',
//            'length' => 'required',
//            'width' => 'required',
//            'height' => 'required',
////            'ground_height' => 'required',
//            'image' => 'required',
//            'year' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $params = [];

        $truck = $this->truck->find($request->truck_id);

        $truck->trailer->update($request->only(
            'make_id','type_id','max_weight','length','width','height',
            'ground_height','image','year'
        ));

        $driver->load('truck.trailer.type','truck.trailer.make');

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);

    }

}