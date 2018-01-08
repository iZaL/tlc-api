<?php


namespace App\Http\Controllers\Api\Shipper;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoadResourceCollection;
use App\Models\Country;
use App\Models\Load;
use App\Models\Shipper;
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
     * LoadsController constructor.
     * @param Shipper $shipperModel
     * @param Load $loadModel
     * @param Country $countryModel
     */
    public function __construct(Shipper $shipperModel, Load $loadModel, Country $countryModel)
    {
        $this->middleware('shipper')->only(['bookLoad']);
        $this->middleware('driver')->only(['getLoads']);
        $this->shipperModel = $shipperModel;
        $this->loadModel = $loadModel;
        $this->countryModel = $countryModel;
    }

    /**
     * @param Request $request
     * @return LoadResourceCollection
     * Get loads for the Authenticated Driver
     */


    /**
     * @param Request $request
     * Passes the Required Data for Create Load Screen
     */
    public function createLoad(Request $request)
    {

        // get trailer types
        // get packing types
        // get customer's saved origins and destinations
        //

        $loads = $this->loadModel->query();


    }

    public function storeLoad(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'shipper_id'              => 'required',
            'trailer_id'              => 'required',
            'origin_location_id'      => 'required',
            'destination_location_id' => 'required',
            'price'                   => 'required',
            'request_documents'       => 'boolean',
            'request_pictures'        => 'boolean',
            'fixed_rate'              => 'boolean',
            'load_date'            => 'required|date'
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()], 422);
        }

        $shipper = Auth::guard('api')->user()->shipper;

        $data = $request->all();

        if ($shipper->canBookDirect()) {
            $data['status'] = 'approved';
        }

        $this->loadModel->create($data);

        return response()->json(['success' => true, 'type' => 'created', 'message' => trans('general.load_created')]);

    }

}