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
            'customer.employees'
        ]);

        switch ($status) {
            case 'pending':
                $loads->where('status', $this->loadModel::STATUS_PENDING);
                break;
            case 'dispatched':
                $loads->where('status', $this->loadModel::STATUS_DISPATCHED);
                break;
            case 'confirmed':
                $loads
//                    ->where('status', '>',$this->loadModel::STATUS_COMPLETED)
                    ->where('status', '<',$this->loadModel::STATUS_COMPLETED)
                ;
                break;
            case 'completed':
                $loads->where('status', $this->loadModel::STATUS_COMPLETED);
                break;
            default:
                $loads->where('status', 'notfound');
        }

        $loads = $loads
            ->where('customer_id',$customer->id)
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
            'trailer_type_id'         => 'required',
            'packaging_id'            => 'required',
            'origin_location_id'      => 'required',
            'destination_location_id' => 'required',
            'request_documents'       => 'boolean',
            'use_own_truck'           => 'boolean',
            'load_date'               => 'required|date',
            'unload_date'             => 'required|date',
            'load_time_from'          => 'required',
            'load_time_to'            => 'required',
            'unload_time_from'        => 'required',
            'unload_time_to'          => 'required',
            'receiver_name'           => 'required',
            'receiver_email'          => 'required',
            'receiver_phone'          => 'required',
            'receiver_mobile'         => 'required',
            'security_passes'         => 'array',
            'packaging_dimension'     => 'array'
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $customer = Auth::guard('api')->user()->customer;

        $data = $request->all();

        $data['load_date'] = Carbon::parse($request->load_date)->toDateString();
        $data['unload_date'] = Carbon::parse($request->unload_date)->toDateString();
        $data['load_time_from'] = Carbon::parse($request->load_time_from)->toTimeString();
        $data['load_time_to'] = Carbon::parse($request->load_time_to)->toTimeString();
        $data['unload_time_from'] = Carbon::parse($request->unload_time_from)->toTimeString();
        $data['unload_time_to'] = Carbon::parse($request->unload_time_to)->toTimeString();

        if ($customer->canBookDirect()) {
            $data['status'] = Load::STATUS_APPROVED;
        }

        $data['packaging_width'] =  $request->packaging_dimension['width'];
        $data['packaging_height'] = $request->packaging_dimension['height'];
        $data['packaging_length'] = $request->packaging_dimension['length'];
        $data['packaging_weight'] = $request->packaging_dimension['weight'];
        $data['packaging_quantity'] = $request->packaging_dimension['quantity'];
        $data['fleet_count'] = $request->trailer_quantity;
        $data['track_id'] = $this->generateTrackID();

        $loadData = array_merge($data, ['customer_id' => $customer->id]);

        $load = $this->loadModel->create($loadData);

        //passes
        if ($request->security_passes) {
            $load->security_passes()->sync($request->security_passes);
        }

        if($request->packaging_images) {

            $images = [];

            foreach ($request->packaging_images as $image) {
                $images[] = ['url' => $image,'type' => 'Packaging','extension' => 'image'];
            }

            $load->documents()->createMany($images);
        }

        $customer->load('loads.security_passes');

        return response()->json([
            'success' => true,
            'load' => new LoadResource($load),
            'customer' => new CustomerResource($customer),
            'load_status' => 'pending'
        ]);

    }

    public function getLoadDetails($loadID)
    {
        $load = $this->loadModel->with([
            'origin.country',
            'destination.country',
            'trailer_type',
            'trips.driver.user',
            'trips.driver.nationalities',
            'packaging',
            'packaging_images',
            'commodity'
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

    public function generateTrackID()
    {
        $randomID = strtoupper(str_random(7));

        $findDuplicate = $this->loadModel->where('track_id',$randomID)->first();

        if($findDuplicate) {
            return $this->generateTrackID();
        }

        return $randomID;

    }
}