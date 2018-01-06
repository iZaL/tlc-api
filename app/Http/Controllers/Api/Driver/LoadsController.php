<?php


namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Http\Resources\LoadsResource;
use App\Http\Resources\RoutesResource;
use App\Http\Resources\UserResource;
use App\Models\Load;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoadsController extends Controller
{
    /**
     * @var Load
     */
    private $loadModel;

    /**
     * LoadsController constructor.
     * @param Load $loadModel
     */
    public function __construct(Load $loadModel)
    {

        $this->loadModel = $loadModel;
    }

    public function getLoadRequests()
    {
        $driver = Auth::guard('api')->user()->driver;
        $driver->load([
            'loads.origin.country',
            'loads.destination.country',
            'loads.trailer'
        ]);

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);
    }

    public function getLoadDetails($loadID)
    {
        $load = $this->loadModel->with([
            'origin.country',
            'destination.country',
            'loads.trailer'
        ])->find($loadID);

        return response()->json(['success'=>true,'data'=>new LoadsResource($load)]);

    }

}