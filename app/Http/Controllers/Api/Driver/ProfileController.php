<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Driver;
use App\Http\Resources\DriverResource;
use App\Http\Resources\UserResource;
use App\Models\Country;
use App\Models\DriverDocument;
use Carbon\Carbon;
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
     * @var DriverDocument
     */
    private $driverDocumentModel;

    /**
     * CountriesController constructor.
     * @param Driver $driverModel
     * @param DriverDocument $driverDocumentModel
     */
    public function __construct(Driver $driverModel,DriverDocument $driverDocumentModel)
    {
        $this->driverModel = $driverModel;
        $this->driverDocumentModel = $driverDocumentModel;
    }

    public function getProfile()
    {

        $user = Auth::guard('api')->user();

        $driver = $user->driver;

        $driver->load([
            'truck.trailer.type',
            'truck.trailer.make',
            'truck.model.make',
            'truck.registration_country.loading_routes',
            'security_passes.country',
            'blocked_list',
            'customer.user',
            'residencies.country',
            'licenses.country',
            'visas.country',
            'nationalities.country'
        ]);

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);

    }

    public function update(Request $request)
    {

        $user = Auth::guard('api')->user();
        $driver = $user->driver;

        $validation = Validator::make($request->all(), [
            'email' => 'required|unique:users,email,' . $user->id,
            'mobile' => 'required|unique:users,mobile,' . $user->id,
            'name' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $user->update($request->only(['name','email','mobile']));

        $driver->update(['mobile' => $request->profile['mobile'], 'phone' => $request->profile['phone']]);

        return response()->json(['success'=>true,'data'=>new UserResource($user)]);

    }


    public function getSecurityPasses(Request $request)
    {
        $driver = Auth::guard('api')->user()->driver;
        $driver->load('security_passes');

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);
    }

    public function updateDocument(Request $request)
    {

        $user = Auth::guard('api')->user();
        $driver = $user->driver;

        $validation = Validator::make($request->all(), [
            'expiry_date' => 'required',
            'number' => 'required',
            'country_id' => 'required',
            'type' => 'required',
            'image' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $payload = $request->only(['id','number','country_id','type','image']);
        $payload['expiry_date'] = Carbon::parse($request->expiry_date)->toDateString();
        $payload['driver_id'] = $driver->id;

        if($request->filled('id')) {
            $document = $this->driverDocumentModel->find($request->id);
            $document->update($payload);
        } else {
            $document = $this->driverDocumentModel->create($payload);
        }

        $driver->load('nationalities','visas','residencies','licenses');

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);

    }

    public function saveSecurityPass(Request $request)
    {

        $user = Auth::guard('api')->user();
        $driver = $user->driver;

        $validation = Validator::make($request->all(), [
            'image' => 'required',
            'expiry_date' => 'required',
            'security_pass_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $image = $request->image;
        $securityPassID = $request->security_pass_id;
        $expiryDate = Carbon::parse($request->expiry_date)->toDateString();
        $driver->security_passes()->syncWithoutDetaching([$securityPassID => [
            'image' => $request->image,
            'expiry_date' => $expiryDate,
        ]]);

        $driver->load('security_passes');

        return response()->json(['success'=>true,'data'=>new DriverResource($driver)]);

    }
}