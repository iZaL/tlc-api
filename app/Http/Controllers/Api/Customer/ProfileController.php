<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Customer;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    private $customerModel;

    /**
     * @param Customer $customerModel
     */
    public function __construct(Customer $customerModel)
    {
        $this->customerModel = $customerModel;
    }

    public function getProfile()
    {
        $user = Auth::guard('api')->user();
        $customer = $user->customer;

        return (new CustomerResource($customer))->additional([
            'success' => true,
            'meta'    => [
                'name_en'    => $user->name_en,
                'name_ar'    => $user->name_ar,
                'name_hi'    => $user->name_hi,
                'address_en' => $customer->address_en,
                'address_ar' => $customer->address_ar,
                'address_hi' => $customer->address_hi,
            ]
        ]);

//        return response()->json(['success'=>true,'data'=> ]);

    }

    public function update(Request $request)
    {
        $user = Auth::guard('api')->user();
        $customer = $user->customer;

        $validation = Validator::make($request->all(), [
            'mobile' => 'required|unique:customers,mobile,' . $customer->id,
            'phone' => 'required|unique:customers,phone,' . $customer->id,
            'email' => 'required|unique:customers,email,' .$customer->id,
            'address_en' => 'max:255',
            'address_ar' => 'max:255',
            'user' => 'array',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $customer->update([
            'mobile' => $request->mobile,
            'phone' => $request->phone,
            'email' => $request->email,
            'address_en' => $request->address_en,
            'address_ar' => $request->address_ar,
        ]);

        $user->update($request->user);

        return (new CustomerResource($customer))->additional([
            'success' => true,
            'meta'    => [
                'name_en'    => $user->name_en,
                'name_ar'    => $user->name_ar,
                'name_hi'    => $user->name_hi,
                'address_en' => $customer->address_en,
                'address_ar' => $customer->address_ar,
                'address_hi' => $customer->address_hi,
            ]
        ]);

    }


}