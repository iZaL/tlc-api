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

        if ($truck = $driver->truck) {
            $driver->truck->update(array_merge($request->all(), $params));
        } else {
            $params['driver_id'] = $driver->id;
            $this->truck->create(array_merge($request->all(), $params));
        }

        $driver->load('truck.trailer', 'truck.model');

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);

    }

}