<?php

namespace App\Http\Controllers\Api\Shipper;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Shipper;
use App\Http\Resources\ShipperResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    private $shipperModel;

    /**
     * @param Shipper $shipperModel
     */
    public function __construct(Shipper $shipperModel)
    {
        $this->shipperModel = $shipperModel;
    }

    public function getProfile()
    {
        $user = Auth::guard('api')->user();
        $shipper = $user->shipper;

        return (new ShipperResource($shipper))->additional([
            'success' => true,
            'meta'    => [
                'name_en'    => $user->name_en,
                'name_ar'    => $user->name_ar,
                'name_hi'    => $user->name_hi,
                'address_en' => $shipper->address_en,
                'address_ar' => $shipper->address_ar,
                'address_hi' => $shipper->address_hi,
            ]
        ]);

//        return response()->json(['success'=>true,'data'=> ]);

    }

    public function update(Request $request)
    {
        $user = Auth::guard('api')->user();
        $shipper = $user->shipper;

        $validation = Validator::make($request->all(), [
            'mobile' => 'required|unique:shippers,mobile,' . $shipper->id,
            'phone' => 'required|unique:shippers,phone,' . $shipper->id,
            'email' => 'required|unique:shippers,email,' .$shipper->id,
            'address_en' => 'max:255',
            'address_ar' => 'max:255',
            'user' => 'array',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $shipper->update([
            'mobile' => $request->mobile,
            'phone' => $request->phone,
            'email' => $request->email,
            'address_en' => $request->address_en,
            'address_ar' => $request->address_ar,
        ]);

        $user->update($request->user);

        return (new ShipperResource($shipper))->additional([
            'success' => true,
            'meta'    => [
                'name_en'    => $user->name_en,
                'name_ar'    => $user->name_ar,
                'name_hi'    => $user->name_hi,
                'address_en' => $shipper->address_en,
                'address_ar' => $shipper->address_ar,
                'address_hi' => $shipper->address_hi,
            ]
        ]);

    }


}