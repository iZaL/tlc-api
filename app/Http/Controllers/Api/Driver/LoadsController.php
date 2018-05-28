<?php


namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Http\Resources\LoadResourceCollection;
use App\Http\Resources\LoadResource;
use App\Models\Country;
use App\Models\Load;
use App\Models\Trip;
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
        $loads = $this->loadModel->with([
            'customer',
            'origin.country',
            'destination.country',
            'trailer_type',
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
        $trailerTypeID = $request->trailer_type_id;

        $driverValidVisaCountries = $driver->valid_visas->pluck('id');
        $driverValidLicenses = $driver->valid_licenses->pluck('id');
        $blockedCustomers = $driver->blocked_list->pluck('id');
        $driverValidPasses = $driver->security_passes->pluck('id');

        $validCountries = $driverValidVisaCountries->intersect($driverValidLicenses);

        $loads =
            DB::table('loads')
                ->join('customer_locations as sl', 'loads.origin_location_id', 'sl.id')
                ->join('customers as s', 'loads.customer_id', 's.id')
                ->leftJoin('load_security_passes as lp', 'loads.id', 'lp.load_id')
                ->leftJoin('drivers as d', 'd.customer_id', 's.id')
                ->when($trailerTypeID, function ($q) use ($trailerTypeID) {
                    $q->where('trailer_type_id', $trailerTypeID);
                })
                ->where('loads.status', Load::STATUS_APPROVED)
                ->where(function ($query) use ($driverValidPasses) {
                    $query
                        ->whereIn('lp.security_pass_id', $driverValidPasses)
                        ->orWhere('lp.security_pass_id', null);
                })
                ->where(function ($query) use ($driver) {
                    $query
                        ->where('d.id', $driver->id)
                        ->orWhere('loads.use_own_truck', 0);
                })
                ->where('loads.origin_location_id', $currentCountry->id)//@todo : fix to truck registration country
                ->whereIn('loads.destination_location_id', $validCountries)
                ->whereNotIn('loads.customer_id', $blockedCustomers)
                ->select('loads.*')
                ->get();

        return response()->json(['success' => true, 'data' => $loads]);
    }

    public function getLoadDetails($loadID)
    {
        $load = $this->loadModel->with([
            'origin.country',
            'destination.country',
            'trailer_type',
            'customer.employees',
            'customer.user',
            'trip.documents'
        ])->find($loadID);

        return response()->json(['success' => true, 'data' => new LoadResource($load)]);

    }


    public function getLoadRequests()
    {
        $driver = Auth::guard('api')->user()->driver;

        $loads = $this->loadModel->whereHas('trip', function ($q) use ($driver) {
            return $q
//                ->where('driver_id', $driver->id)
                ->where('status','<',Trip::STATUS_CONFIRMED)
                ;
        })->with([
            'trip',
            'origin.country',
            'destination.country',
            'trailer_type'
        ])
            ->get()
        ;

        return response()->json(['success' => true, 'driver' => new DriverResource($driver), 'loads' => LoadResource::collection($loads)]);

    }

    /**
     * get working load
     */
    public function getCurrentLoad()
    {
        $driver = Auth::guard('api')->user()->driver;

        $load = $this->loadModel->whereHas('trips', function ($q) use ($driver) {
            return $q
                ->where('driver_id', $driver->id)
                ->ofStatus(Trip::STATUS_ENROUTE)
                ;
        })->with([
            'trips',
            'origin.country',
            'destination.country',
            'trailer_type'
        ])
            ->ofStatus(Trip::STATUS_ENROUTE)
            ->limit(1)
            ->first()
        ;

        return response()->json(['success' => true, 'driver' => new DriverResource($driver), 'load' => new LoadResource($load)]);

    }

    public function getUpcomingLoads()
    {
        $driver = auth()->user()->guard('api')->driver;
        $load = $this->loadModel->whereHas('trips', function ($q) use ($driver) {
            return $q
                ->where('driver_id', $driver->id)//                ->where('status',''); //@todo
                ;
        })->with([
            'trips',
            'origin.country',
            'destination.country',
            'trailer_type'
        ])->paginate(10);

        return response()->json(['success' => true, 'data' => LoadResource::collection($load)]);

    }

}