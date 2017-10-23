<?php


namespace App\Http\Controllers\Api;

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

    public function getLoads(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'current_country' => 'required'
        ]);

        $driver = Auth::guard('api')->user()->driver;
        $currentCountry = $this->countryModel->where('abbr', $request->current_country)->first();
        $trailerID = $request->trailer_id;

        $driverValidVisaCountries = $driver->validVisas->pluck('id');
        $driverValidPasses = $driver->passes->pluck('id');
        $driverValidLicenses = $driver->validLicenses->pluck('id');

//        $validCountries = $driverValidVisaCountries->intersect($driverValidLicenses);

        $loads = DB::table('loads')
            ->join('locations', function ($join) use ($currentCountry,$driverValidVisaCountries) {
                $join
                    ->on('loads.origin_location_id', '=', 'locations.id')
                    ->where('loads.origin_location_id', $currentCountry->id)
                    ->whereIn('loads.destination_location_id', $driverValidVisaCountries);
                ;
            })
            ->leftJoin('load_passes', function ($join) use ($driverValidPasses) {
                $join
                    ->on('loads.id', '=', 'load_passes.load_id')
                ;
            })
            ->where('loads.status', 'waiting')
            ->where(function ($query) use ($driverValidPasses) {
                $query
                    ->whereIn('load_passes.pass_id', $driverValidPasses)
                    ->orWhere('load_passes.pass_id', null)
                ;
            })
        ;

        if ($trailerID) {
            $loads = $loads->where('trailer_id', $trailerID);
        }

        $loads = $loads

            ->groupBy('loads.id')
            ->select('loads.*')
        ;

        $loads = $loads->paginate(20);

        return new LoadResourceCollection($loads);
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