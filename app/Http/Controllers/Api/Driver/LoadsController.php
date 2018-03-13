<?php


namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoadResourceCollection;
use App\Http\Resources\LoadResource;
use App\Models\Country;
use App\Models\Load;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoadsController extends Controller
{
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
     * @param Load $loadModel
     * @param Country $countryModel
     */
    public function __construct(Load $loadModel, Country $countryModel)
    {

        $this->loadModel = $loadModel;
        $this->countryModel = $countryModel;
    }

    public function getLoadsByStatus($status, Request $request)
    {
        $shipper = Auth::guard('api')->user()->shipper;
        $loads = $this->loadModel->with([
            'shipper',
            'origin.country',
            'destination.country',
            'trailer',
            'packaging',
        ])->where('status', $status)->paginate(10);
        return response()->json(['success' => true, 'data' => LoadResource::collection($loads)]);
    }

    public function getLoads(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'current_country' => 'required',
        ]);

        $driver = Auth::guard('api')->user()->driver;

        $currentCountry = $this->countryModel->where('abbr', $request->current_country)->first();
        $trailerID = $request->trailer_id;

        $driverValidVisaCountries = $driver->valid_visas->pluck('id');
        $driverValidLicenses = $driver->valid_licenses->pluck('id');
        $blockedShippers = $driver->blocked_list->pluck('id');
        $driverValidPasses = $driver->passes->pluck('id');

        $validCountries = $driverValidVisaCountries->intersect($driverValidLicenses);

        $loads =
            DB::table('loads')
                ->join('shipper_locations as sl', 'loads.origin_location_id', 'sl.id')
                ->join('shippers as s', 'loads.shipper_id', 's.id')
                ->leftJoin('load_passes as lp', 'loads.id', 'lp.load_id')
                ->leftJoin('drivers as d', 'd.shipper_id', 's.id')
                ->when($trailerID, function ($q) use ($trailerID) {
                    $q->where('trailer_id', $trailerID);
                })
                ->where('loads.status', 'waiting')
                ->where(function ($query) use ($driverValidPasses) {
                    $query
                        ->whereIn('lp.pass_id', $driverValidPasses)
                        ->orWhere('lp.pass_id', null);
                })
                ->where(function ($query) use ($driver) {
                    $query
                        ->where('d.id', $driver->id)
                        ->orWhere('loads.use_own_truck', 0);
                })
                ->where('loads.origin_location_id', $currentCountry->id)
                ->whereIn('loads.destination_location_id', $validCountries)
                ->whereNotIn('loads.shipper_id', $blockedShippers)
                ->select('loads.*')
                ->paginate(20);

        return new LoadResourceCollection($loads);
    }


    public function getLoadDetails($loadID)
    {
        $load = $this->loadModel->with([
            'origin.country',
            'destination.country',
            'trailer'
        ])->find($loadID);

        return response()->json(['success'=>true,'data'=>new LoadResource($load)]);

    }

}