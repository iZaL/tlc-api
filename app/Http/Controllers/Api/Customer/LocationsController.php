<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Customer;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserResource;
use App\Models\Country;
use App\Models\CustomerLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationsController extends Controller
{

    private $customerModel;
    /**
     * @var CustomerLocation
     */
    private $locationModel;
    /**
     * @var Country
     */
    private $countryModel;

    /**
     * @param Customer $customerModel
     * @param CustomerLocation $locationModel
     * @param Country $countryModel
     */
    public function __construct(Customer $customerModel, CustomerLocation $locationModel,Country $countryModel)
    {
        $this->customerModel = $customerModel;
        $this->locationModel = $locationModel;
        $this->countryModel = $countryModel;
    }

    public function index()
    {
        $user = Auth::guard('api')->user();
        $customer = $user->customer;
        $customer->load('locations.country');

        return (new CustomerResource($customer))->additional([
            'success' => true,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();

        $customer = $user->customer;

        $location = null;

        $validation = Validator::make($request->all(), [
            'address' => 'required|max:100',
            'city' => 'required|max:100',
            'state' => 'required|max:100' ,
            'country' => 'required|max:10' ,
            'latitude' => 'required',
            'longitude' => 'required',
            'type' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $country = $this->countryModel->whereAbbr($request->country)->first();

        if(!$country) {
            return response()->json(['success' => false, 'message' => 'invalid country']);
        }

        $data = [
            'address_en'     => $request->address,
            'address_ar'     => $request->address,
            'city_en'      => $request->city,
            'city_ar'      => $request->city,
            'state_en'     => $request->state,
            'state_ar'     => $request->state,
            'latitude'   => $request->latitude,
            'longitude'   => $request->longitude,
            'country_id' => $country->id,
            'type' => $request->type
        ];

        if($request->has('address_id')) {
            $location = $this->locationModel->find($request->address_id);
            $location->update($data);
        } else {
            $location = $customer->locations()->create($data);
        }

        $customer->load('locations.country');

        return (new CustomerResource($user))->additional([
            'success' => true,
            'address_id'=>$location->id,
        ]);

    }


}