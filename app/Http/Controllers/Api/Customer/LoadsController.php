<?php


namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Http\Resources\LoadResource;
use App\Http\Resources\LoadResourceCollection;
use App\Http\Resources\PackagingResource;
use App\Http\Resources\SecurityPassResource;
use App\Http\Resources\CustomerLocationResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\TrailerResource;
use App\Models\Country;
use App\Models\Driver;
use App\Models\Load;
use App\Models\Packaging;
use App\Models\SecurityPass;
use App\Models\Customer;
use App\Models\Trailer;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoadsController extends Controller
{
    /**
     * @var Customer
     */
    private $customerModel;
    /**
     * @var Load
     */
    private $loadModel;
    /**
     * @var Country
     */
    private $countryModel;
    /**
     * @var Trailer
     */
    private $trailerModel;
    /**
     * @var Packaging
     */
    private $packagingModel;
    /**
     * @var SecurityPass
     */
    private $passModel;
    /**
     * @var Driver
     */
    private $driverModel;
    /**
     * @var Trip
     */
    private $tripModel;

    /**
     * LoadsController constructor.
     * @param Driver $driverModel
     * @param Customer $customerModel
     * @param Load $loadModel
     * @param Country $countryModel
     * @param Trailer $trailerModel
     * @param Packaging $packagingModel
     * @param SecurityPass $passModel
     * @param Trip $tripModel
     */
    public function __construct(Driver $driverModel, Customer $customerModel, Load $loadModel, Country $countryModel, Trailer $trailerModel, Packaging $packagingModel, SecurityPass $passModel,Trip $tripModel)
    {
        $this->middleware('customer')->only(['bookLoad']);
        $this->middleware('driver')->only(['getLoads']);
        $this->customerModel = $customerModel;
        $this->loadModel = $loadModel;
        $this->countryModel = $countryModel;
        $this->trailerModel = $trailerModel;
        $this->packagingModel = $packagingModel;
        $this->passModel = $passModel;
        $this->driverModel = $driverModel;
        $this->tripModel = $tripModel;
    }

    public function getLoadsByStatus($status, Request $request)
    {
        $customer = Auth::guard('api')->user()->customer;

        $loads = $this->loadModel->with([
            'trips',
            'origin.country',
            'destination.country',
            'packaging',
            'customer'
        ]);

        switch ($status) {
            case 'pending':
                $s = $this->tripModel::STATUS_APPROVED;
                break;
            case 'confirmed':
                $s = $this->tripModel::STATUS_CONFIRMED;
                break;
            case 'completed':
                $s = $this->tripModel::STATUS_COMPLETED;
                break;
            default:
                $s = $this->tripModel::STATUS_REJECTED;
        }

        $loads = $loads
            ->where('customer_id',$customer->id)
            ->where('status', $s)
            ->paginate(10)
        ;

        return response()->json([
            'success' => true,
            'loads' => LoadResource::collection($loads),
            'load_status' => $status,
            'customer' => new CustomerResource($customer)
        ]);

    }

    public function getLoadAddData(Request $request)
    {
        // get trailers
        // get packaging
        $customer = Auth::guard('api')->user()->customer;

        $trailers = $this->trailerModel->active()->get();
        $packaging = $this->packagingModel->active()->get();
        $passes = $this->passModel->with(['country'])->active()->get();
        $locations = $customer->locations;

        $locations->load('country');

        $customer->locations = CustomerLocationResource::collection($locations);

        return response()->json(['success' => true, 'data' => [
            'trailers'  => TrailerResource::collection($trailers),
            'packaging' => PackagingResource::collection($packaging),
            'security_passes'    => SecurityPassResource::collection($passes),
            'customer'   => new CustomerResource($customer)
        ]]);

    }

    public function storeLoad(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'trailer_type_id'              => 'required',
            'packaging_id'            => 'required',
            'origin_location_id'      => 'required',
            'destination_location_id' => 'required',
            'request_documents'       => 'boolean',
            'use_own_truck'           => 'boolean',
            'load_date'               => 'required|date',
//            'load_time'               => 'required',
            'receiver_name'           => 'required',
            'receiver_email'          => 'required',
            'receiver_phone'          => 'required',
            'receiver_mobile'         => 'required',
//            'weight'                  => 'required',
            'security_passes'         => 'array',
            'packaging_dimension' => 'array'
        ]);

        //        array:10 [
        //  "trailer_id" => 1
        //  "origin_location_id" => 1
        //  "destination_location_id" => 1
        //  "price" => "200.00"
        //  "distance" => "100"
        //  "request_documents" => 0
        //  "request_pictures" => 0
        //  "fixed_rate" => 1
        //  "load_date" => "2017-10-19"
        //]

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $customer = Auth::guard('api')->user()->customer;

        $data = $request->all();

        $data['load_date'] = Carbon::parse($request->load_date)->toDateString();

        if ($customer->canBookDirect()) {
            $data['status'] = Load::STATUS_APPROVED;
        }

        $data['packaging_width'] =  $request->packaging_dimension['width'];
        $data['packaging_height'] = $request->packaging_dimension['height'];
        $data['packaging_length'] = $request->packaging_dimension['length'];
        $data['packaging_weight'] = $request->packaging_dimension['weight'];
        $data['packaging_quantity'] = $request->packaging_dimension['quantity'];

        $loadData = array_merge($data, ['customer_id' => $customer->id]);


//      $data['packaging_width'] = $request->packaging_dimension['width'];
//        $data['packaging_height'] = $request->packaging_dimension['height'];
//        $data['packaging_length'] = $request->packaging_dimension['length'];
//        $data['packaging_weight'] = $request->packaging_dimension['weight'];
//        $data['packaging_quantity'] = $request->packaging_dimension['quantity'];

        $load = $this->loadModel->create($loadData);

        //passes
        if ($request->security_passes) {
            $load->security_passes()->sync($request->security_passes);
        }

        $customer->load('loads.security_passes');

        return response()->json([
            'success' => true,
            'load' => new LoadResource($load),
            'customer' => new CustomerResource($customer),
            'load_status' => 'pending'
        ]);
//        return response()->json(['success' => true, 'data' => new CustomerResource($customer), 'type' => 'created', 'message' => trans('general.load_created')]);

    }

    public function getLoadDetails($loadID)
    {
        $load = $this->loadModel->with([
            'origin.country',
            'destination.country',
            'trailer_type',
            'trips.driver.user',
            'trips.driver.nationalities',
        ])->find($loadID);

        return response()->json(['success'=>true,'data'=>new LoadResource($load)]);
    }

    public function getLoadDrivers($loadID)
    {

        $load = $this->loadModel->find($loadID);

        $drivers = $this->driverModel->has('user')->with(['user'])->get();
        $driversCollection = DriverResource::collection($drivers);

        $load->drivers = $driversCollection;

        return response()->json(['success'=>true,'data'=>new LoadResource($load)]);

    }
}