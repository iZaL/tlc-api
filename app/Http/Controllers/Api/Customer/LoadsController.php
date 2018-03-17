<?php


namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoadResource;
use App\Http\Resources\LoadResourceCollection;
use App\Http\Resources\PackagingResource;
use App\Http\Resources\PassResource;
use App\Http\Resources\CustomerLocationResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\TrailerResource;
use App\Models\Country;
use App\Models\Load;
use App\Models\Packaging;
use App\Models\Pass;
use App\Models\Customer;
use App\Models\Trailer;
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
     * @var Pass
     */
    private $passModel;

    /**
     * LoadsController constructor.
     * @param Customer $customerModel
     * @param Load $loadModel
     * @param Country $countryModel
     * @param Trailer $trailerModel
     * @param Packaging $packagingModel
     * @param Pass $passModel
     */
    public function __construct(Customer $customerModel, Load $loadModel, Country $countryModel, Trailer $trailerModel, Packaging $packagingModel, Pass $passModel)
    {
        $this->middleware('customer')->only(['bookLoad']);
        $this->middleware('driver')->only(['getLoads']);
        $this->customerModel = $customerModel;
        $this->loadModel = $loadModel;
        $this->countryModel = $countryModel;
        $this->trailerModel = $trailerModel;
        $this->packagingModel = $packagingModel;
        $this->passModel = $passModel;
    }

    public function getLoadsByStatus($status, Request $request)
    {
        $customer = Auth::guard('api')->user()->customer;
        $loads = $this->loadModel->with([
            'customer',
            'origin.country',
            'destination.country',
            'trailer',
            'packaging',
        ])->where('status', $status)->paginate(10);
        return response()->json(['success' => true, 'data' => LoadResource::collection($loads)]);
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
            'passes'    => PassResource::collection($passes),
            'customer'   => new CustomerResource($customer)
        ]]);

    }

    public function storeLoad(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'trailer_id'              => 'required',
            'packaging_id'            => 'required',
            'origin_location_id'      => 'required',
            'destination_location_id' => 'required',
            'request_documents'       => 'boolean',
            'use_own_truck'           => 'boolean',
            'load_date'               => 'required|date',
            'load_time'               => 'required',
            'receiver_name'           => 'required',
            'receiver_email'          => 'required',
            'receiver_phone'          => 'required',
            'receiver_mobile'         => 'required',
            'weight'                  => 'required',
            'passes'                  => 'array'
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
            $data['status'] = 'approved';
        }

        $loadData = array_merge($data, ['customer_id' => $customer->id]);

        $load = $this->loadModel->create($loadData);

        //passes
        if ($request->passes) {
            $load->passes()->sync($request->passes);
        }

        $customer->load('loads.passes');

        return response()->json(['success' => true, 'data' => new CustomerResource($customer), 'type' => 'created', 'message' => trans('general.load_created')]);

    }

}