<?php


namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoutesController extends Controller
{

    /**
     * @return UserResource
     * Get Driver Routes
     */
    public function getRoutes()
    {
        $user = Auth::guard('api')->user();

        $user->load(['driver.routes','driver.residence.loading_routes','driver.available_routes']);

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

        $driver->routes()->toggle([$request->route_id]);

        $user->load(['driver.routes']);

        return new UserResource($user);

    }

}