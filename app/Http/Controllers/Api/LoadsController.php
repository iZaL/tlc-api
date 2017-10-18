<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Models\Shipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * LoadsController constructor.
     * @param Shipper $shipperModel
     * @param Load $loadModel
     */
    public function __construct(Shipper $shipperModel, Load $loadModel)
    {
        $this->middleware('shipper');
        $this->shipperModel = $shipperModel;
        $this->loadModel = $loadModel;
    }

    public function bookLoad(Request $request)
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
            'scheduled_at'            => 'required|date'
        ]);

        if ($validation->fails()) {
            return response()->json(['success' => false, 'message' => $validation->errors()->first()],422);
        }

        $user = Auth::guard('api')->user();

        $this->loadModel->create($request->all());

        return response()->json(['success' => true, 'type' => 'created', 'message' => trans('general.load_created')]);

    }

}