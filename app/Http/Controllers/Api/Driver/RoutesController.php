<?php


namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Http\Resources\RoutesResource;
use App\Http\Resources\UserResource;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoutesController extends Controller
{
    /**
     * @var Route
     */
    private $routeModel;

    /**
     * RoutesController constructor.
     * @param Route $routeModel
     */
    public function __construct(Route $routeModel)
    {
        $this->routeModel = $routeModel;
    }

    /**
     * Get Driver Routes
     */
    public function getRoutes()
    {
        $driver = Auth::guard('api')->user()->driver;

        $driver->load(['routes.drivers','routes.transits','visas','licenses']);

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);
    }

    public function saveRoute(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'route_id'  => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $driver = Auth::guard('api')->user()->driver;

        $driver->routes()->sync([$request->route_id]);

        //@todo:
//        $driver->load(['truck.registration_country.loading_routes']);

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);

    }

    public function getRouteTransits($routeID)
    {
        $route = $this->routeModel->with(['transits'])->find($routeID);

        return response()->json(['success'=>true,'data'=>new RoutesResource($route)]);

    }

}