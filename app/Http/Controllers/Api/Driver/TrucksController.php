<?php


namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoadResourceCollection;
use App\Http\Resources\UserResource;
use App\Models\Country;
use App\Models\Load;
use App\Models\Shipper;
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
     * TrucksController constructor.
     * @param TruckMake $truckMake
     * @param TruckModel $truckModel
     * @param Truck $truck
     */
    public function __construct(TruckMake $truckMake, TruckModel $truckModel, Truck $truck)
    {
        $this->truckMake = $truckMake;
        $this->truckModel = $truckModel;
        $this->truck = $truck;
    }

    public function getTrailerMakes()
    {
        $user = Auth::guard('api')->user();

        $driver = $user->driver;


    }

}