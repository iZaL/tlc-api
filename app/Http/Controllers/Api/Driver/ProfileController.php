<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Driver;
use App\Http\Resources\DriverResource;
use App\Http\Resources\UserResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * @var Country
     */
    private $countryModel;
    /**
     * @var Driver
     */
    private $driverModel;

    /**
     * CountriesController constructor.
     * @param Driver $driverModel
     */
    public function __construct(Driver $driverModel)
    {
        $this->driverModel = $driverModel;
    }

    public function getProfile()
    {

        $user = Auth::guard('api')->user();
//        $user = Auth::loginUsingId(2);

        $driver = $user->driver;

        $driver->load([
            'nationality',
//            'residence.loading_routes.drivers',
            'truck.trailer',
            'truck.model',
            'truck.make',
            'truck.registration_country.loading_routes',
            'passes',
            'blocked_list',
            'customer.user',
            'residencies',
        ]);

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);

    }

    public function update(Request $request)
    {
        $driver = Auth::guard('api')->user()->driver;

        $validation = Validator::make($request->all(), [
            'mobile'                 => 'required|unique:drivers,mobile,' . $driver->id,
            'nationality_country_id' => 'required',
//            'residence_country_id'   => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $driver->update($request->all());

//        $driver->load('nationality', 'residence');
        $driver->load('nationality');

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);

    }


}