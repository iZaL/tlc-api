<?php


namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Managers\DriverManager;
use App\Http\Managers\LoadManager;
use App\Http\Resources\DriverResource;
use App\Http\Resources\LoadResource;
use App\Http\Resources\LoadResourceCollection;
use App\Http\Resources\PackagingResource;
use App\Http\Resources\PassResource;
use App\Http\Resources\CustomerLocationResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\TrailerResource;
use App\Models\Country;
use App\Models\Driver;
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

class LoadDriversController extends Controller
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
     * @var Driver
     */
    private $driverModel;

    /**
     * LoadsController constructor.
     * @param Customer $customerModel
     * @param Load $loadModel
     * @param Country $countryModel
     * @param Trailer $trailerModel
     * @param Packaging $packagingModel
     * @param Pass $passModel
     * @param Driver $driverModel
     */
    public function __construct(Customer $customerModel, Load $loadModel, Country $countryModel, Trailer $trailerModel, Packaging $packagingModel, Pass $passModel, Driver $driverModel)
    {
        $this->customerModel = $customerModel;
        $this->loadModel = $loadModel;
        $this->countryModel = $countryModel;
        $this->trailerModel = $trailerModel;
        $this->packagingModel = $packagingModel;
        $this->passModel = $passModel;
        $this->driverModel = $driverModel;
    }

    /** @todo
     *
     * check whether the load is valid
     * is not expired
     * no of fleets are not booked
     * has eno
     *
     * ============================
     * check whether the customer
     * has enough credits
     * is active
     * is not blocked by tlc
     * is
     *
     */

    /**
     * @param $loadID
     * @return \Illuminate\Http\JsonResponse
     * After Posting a Load, Hit this method to fetch Drivers Who are ready to load
     */
    public function searchDriversForLoad($loadID)
    {
        $load = $this->loadModel->with(['customer'])->find($loadID);

        $driverManager = new DriverManager($this->driverModel);

        $availableDrivers = $driverManager->getAvailableDrivers();
        $driversWhoHasTrips = $driverManager->getDriversWhoHasTrips($load->load_date);
        $driversWhoAreBlockedByCustomer = $driverManager->getDriversWhoAreBlockedByCustomer($load->customer->id);


        $excludingDrivers = collect([$driversWhoHasTrips,$driversWhoAreBlockedByCustomer])->flatten();

        $drivers = $availableDrivers->diff($excludingDrivers);
        $drivers = $this->driverModel->whereIn('id',$drivers)->get();

        return response()->json(['success' => true, 'data' => $drivers]);

//        $loads =
//            DB::table('loads')
//                ->join('customer_locations as sl', 'loads.origin_location_id', 'sl.id')
//                ->join('customers as s', 'loads.customer_id', 's.id')
//                ->leftJoin('load_passes as lp', 'loads.id', 'lp.load_id')
//                ->leftJoin('drivers as d', 'd.customer_id', 's.id')
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
//                ->whereNotIn('loads.customer_id', $blockedCustomers)
//                ->select('loads.*')
//                ->paginate(20);
//        $driverValidVisaCountries = $driver->valid_visas->pluck('id');
//        $driverValidLicenses = $driver->valid_licenses->pluck('id');
//        $blockedCustomers = $driver->blocked_list->pluck('id');
//        $driverValidPasses = $driver->passes->pluck('id');


    }

}