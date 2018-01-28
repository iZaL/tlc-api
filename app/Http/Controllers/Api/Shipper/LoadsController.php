<?php


namespace App\Http\Controllers\Api\Shipper;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoadResourceCollection;
use App\Http\Resources\PackagingResource;
use App\Http\Resources\PassResource;
use App\Http\Resources\ShipperLocationResource;
use App\Http\Resources\ShipperResource;
use App\Http\Resources\TrailerResource;
use App\Models\Country;
use App\Models\Load;
use App\Models\Packaging;
use App\Models\Pass;
use App\Models\Shipper;
use App\Models\Trailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoadsController extends Controller
{
    /**
     * @var Shipper
     */
    private $shipperModel;
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
     * @param Shipper $shipperModel
     * @param Load $loadModel
     * @param Country $countryModel
     * @param Trailer $trailerModel
     * @param Packaging $packagingModel
     * @param Pass $passModel
     */
    public function __construct(Shipper $shipperModel, Load $loadModel, Country $countryModel, Trailer $trailerModel, Packaging $packagingModel, Pass $passModel)
    {
        $this->middleware('shipper')->only(['bookLoad']);
        $this->middleware('driver')->only(['getLoads']);
        $this->shipperModel = $shipperModel;
        $this->loadModel = $loadModel;
        $this->countryModel = $countryModel;
        $this->trailerModel = $trailerModel;
        $this->packagingModel = $packagingModel;
        $this->passModel = $passModel;
    }

    public function getLoadAddData(Request $request)
    {
        // get trailers
        // get packaging
        $shipper = Auth::guard('api')->user()->shipper;

        $trailers = $this->trailerModel->active()->get();
        $packaging = $this->packagingModel->active()->get();
        $passes = $this->passModel->with(['country'])->active()->get();
        $locations = $shipper->locations;

        $locations->load('country');

        $shipper->locations = ShipperLocationResource::collection($locations);

        return response()->json(['success' => true, 'data' => [
            'trailers'  => TrailerResource::collection($trailers),
            'packaging' => PackagingResource::collection($packaging),
            'passes'    => PassResource::collection($passes),
            'shipper'   => new ShipperResource($shipper)
        ]]);

    }

    public function createLoad(Request $request)
    {

        // get trailer types
        // get packing types
        // get customer's saved origins and destinations
        //

        $loads = $this->loadModel->query();


        return response()->json(['success' => true, 'data' => [
            ''
        ]]);

    }

    public function storeLoad(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'shipper_id'              => 'required',
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
            'passes'                  => 'required|array'
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $shipper = Auth::guard('api')->user()->shipper;

        $data = $request->all();

        if ($shipper->canBookDirect()) {
            $data['status'] = 'approved';
        }


        $load = $this->loadModel->create($data);

        //passes
        if ($request->passes) {
            $load->passes()->sync($request->passes);
        }

        $shipper->load('loads');

        return response()->json(['success' => true, 'data' => new ShipperResource($shipper), 'type' => 'created', 'message' => trans('general.load_created')]);

    }

}