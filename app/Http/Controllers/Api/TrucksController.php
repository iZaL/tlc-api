<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoadResourceCollection;
use App\Http\Resources\UserResource;
use App\Models\Country;
use App\Models\Load;
use App\Models\Shipper;
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

    public function getMakesModels(Request $request)
    {
        $truckMakes = $this->truckMake->active()->get();
        $truckModels = $this->truckModel->active()->get();

        return response()->json(['success' => true, 'makes' => $truckMakes, 'models' => $truckModels]);
    }

    public function getTrailers()
    {
        $trailers = $this->trailer->active()->get();

        return response()->json(['success' => true, 'data' => $trailers]);

    }

    public function getTrailerMakes()
    {
        $trailerMakes = $this->trailerMake->active()->get();

        return response()->json(['success' => true, 'data' => $trailerMakes]);

    }

}