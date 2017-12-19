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

class RoutesController extends Controller
{

    public function __construct()
    {
    }

    public function getRoutes()
    {
        $user = Auth::guard('api')->user();
//        $driver = $user->driver;

        $user->load('driver', 'driver.routes');

        return new UserResource($user);

    }

    public function saveRoute(Request $request)
    {
        $user = Auth::guard('api')->user();
        $driver = $user->driver;

        $validation = Validator::make($request->all(), [
            'route_id'  => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $driver->routes()->syncWithoutDetaching([$request->route_id]);

        $user->load(['driver.routes']);

        return new UserResource($user);

    }

}