<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriversController extends Controller
{
    /**
     * @var Driver
     */
    private $driverModel;

    /**
     * DriversController constructor.
     * @param Driver $driverModel
     */
    public function __construct(Driver $driverModel)
    {
        $this->driverModel = $driverModel;
    }

    public function getDrivers(Request $request)
    {
        $drivers = $this->driverModel->with(['user'])->paginate(100);

        return response()->json(['success'=>true,'data' => DriverResource::collection($drivers)]);
    }

    public function getBlockedDrivers(Request $request)
    {
        $customer = auth()->guard('api')->user()->customer;

        $customer->load('blocked_drivers.user');

        return response()->json(['success'=>true,'data'=> new CustomerResource($customer)]);


    }

}

