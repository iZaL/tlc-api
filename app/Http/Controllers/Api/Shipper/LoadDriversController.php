<?php


namespace App\Http\Controllers\Api\Shipper;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Http\Resources\LoadResource;
use App\Http\Resources\LoadResourceCollection;
use App\Http\Resources\PackagingResource;
use App\Http\Resources\PassResource;
use App\Http\Resources\ShipperLocationResource;
use App\Http\Resources\ShipperResource;
use App\Http\Resources\TrailerResource;
use App\Models\Country;
use App\Models\Driver;
use App\Models\Load;
use App\Models\Packaging;
use App\Models\Pass;
use App\Models\Shipper;
use App\Models\Trailer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoadDriversController extends Controller
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
     * @var Driver
     */
    private $driverModel;

    /**
     * LoadsController constructor.
     * @param Shipper $shipperModel
     * @param Load $loadModel
     * @param Country $countryModel
     * @param Trailer $trailerModel
     * @param Packaging $packagingModel
     * @param Pass $passModel
     * @param Driver $driverModel
     */
    public function __construct(Shipper $shipperModel, Load $loadModel, Country $countryModel, Trailer $trailerModel, Packaging $packagingModel, Pass $passModel, Driver $driverModel)
    {
        $this->shipperModel = $shipperModel;
        $this->loadModel = $loadModel;
        $this->countryModel = $countryModel;
        $this->trailerModel = $trailerModel;
        $this->packagingModel = $packagingModel;
        $this->passModel = $passModel;
        $this->driverModel = $driverModel;
    }

    public function getDriversForLoad($loadID)
    {
        $load = $this->loadModel->find($loadID);

        /** check whether the load is valid
         * is not expired
         * no of fleets are not booked
         * has eno
         */

        /** check whether the shipper
         * has enough credits
         * is active
         * is not blocked by tlc
         * is
         */

        /** get drivers
         * who are active
         * who are not offline
         * who are not blocked by customer
         * who are not blocked by tlc
         * who are not on other trips
         * who has valid visas (not expired) to destination country and transit country
         * who has valid licenses (not expired)
         * who has valid truck, trailer (length,width,height,capacity) depending on the load dimension
         * who has truck registered on same country as load origin country
         * who has added the load route in their route list
         * who has valid gate passes to the load destination if required
         */



        $drivers = DB::table('drivers')
            ->join('');

        return response()->json(['success' => true, 'data' => DriverResource::collection($drivers)]);


//        $loads =
//            DB::table('loads')
//                ->join('shipper_locations as sl', 'loads.origin_location_id', 'sl.id')
//                ->join('shippers as s', 'loads.shipper_id', 's.id')
//                ->leftJoin('load_passes as lp', 'loads.id', 'lp.load_id')
//                ->leftJoin('drivers as d', 'd.shipper_id', 's.id')
//                ->when($trailerID, function ($q) use ($trailerID) {
//                    $q->where('trailer_id', $trailerID);
//                })
//                ->where('loads.status', 'waiting')
//                ->where(function ($query) use ($driverValidPasses) {
//                    $query
//                        ->whereIn('lp.pass_id', $driverValidPasses)
//                        ->orWhere('lp.pass_id', null);
//                })
//                ->where(function ($query) use ($driver) {
//                    $query
//                        ->where('d.id', $driver->id)
//                        ->orWhere('loads.use_own_truck', 0);
//                })
//                ->where('loads.origin_location_id', $currentCountry->id)
//                ->whereIn('loads.destination_location_id', $validCountries)
//                ->whereNotIn('loads.shipper_id', $blockedShippers)
//                ->select('loads.*')
//                ->paginate(20);
//        $driverValidVisaCountries = $driver->valid_visas->pluck('id');
//        $driverValidLicenses = $driver->valid_licenses->pluck('id');
//        $blockedShippers = $driver->blocked_list->pluck('id');
//        $driverValidPasses = $driver->passes->pluck('id');


    }

}