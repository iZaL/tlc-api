<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoadResourceCollection;
use App\Http\Resources\TrailerMakeResource;
use App\Http\Resources\TrailerTypeResource;
use App\Http\Resources\TruckMakeResource;
use App\Http\Resources\TruckModelResource;
use App\Http\Resources\UserResource;
use App\Models\Country;
use App\Models\Load;
use App\Models\Customer;
use App\Models\Trailer;
use App\Models\TrailerMake;
use App\Models\TrailerType;
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
     * @var TrailerType
     */
    private $trailerType;

    /**
     * TrucksController constructor.
     * @param TruckMake $truckMake
     * @param TruckModel $truckModel
     * @param Truck $truck
     * @param TrailerMake $trailerMake
     * @param Trailer $trailer
     * @param TrailerType $trailerType
     */
    public function __construct(TruckMake $truckMake, TruckModel $truckModel, Truck $truck, TrailerMake $trailerMake,Trailer $trailer,TrailerType $trailerType)
    {
        $this->truckMake = $truckMake;
        $this->truckModel = $truckModel;
        $this->truck = $truck;
        $this->trailerMake = $trailerMake;
        $this->trailer = $trailer;
        $this->trailerType = $trailerType;
    }

    public function getMakesModels(Request $request)
    {
        $truckMakes = $this->truckMake->with(['models'])->active()->get();
        return response()->json(['success' => true, 'makes' => TruckMakeResource::collection($truckMakes)]);
    }

    public function getTrailers()
    {
        $trailers = $this->trailer->active()->get();
        return response()->json(['success' => true, 'data' => $trailers]);
    }

    public function getTrailerMakes()
    {
        $trailerMakes = $this->trailerMake->active()->get();
        return response()->json(['success' => true, 'data' => TrailerMakeResource::collection($trailerMakes)]);
    }

    public function getTrailerTypes()
    {
        $trailerTypes = $this->trailerType->active()->get();
        return response()->json(['success' => true, 'data' => TrailerTypeResource::collection($trailerTypes)]);
    }

}