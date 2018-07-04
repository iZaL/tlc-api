<?php


namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Managers\DriverManager;
use App\Managers\LoadManager;
use App\Managers\RouteManager;
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
use App\Models\DriverDocument;
use App\Models\Load;
use App\Models\Packaging;
use App\Models\SecurityPass;
use App\Models\Customer;
use App\Models\Route;
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
     * @var SecurityPass
     */
    private $passModel;
    /**
     * @var Driver
     */
    private $driverModel;
    /**
     * @var Route
     */
    private $routeModel;

    /**
     * LoadsController constructor.
     * @param Customer $customerModel
     * @param Load $loadModel
     * @param Country $countryModel
     * @param Trailer $trailerModel
     * @param Packaging $packagingModel
     * @param SecurityPass $passModel
     * @param Driver $driverModel
     * @param Route $routeModel
     */
    public function __construct(Customer $customerModel, Load $loadModel, Country $countryModel, Trailer $trailerModel, Packaging $packagingModel, SecurityPass $passModel, Driver $driverModel, Route $routeModel)
    {
        $this->customerModel = $customerModel;
        $this->loadModel = $loadModel;
        $this->countryModel = $countryModel;
        $this->trailerModel = $trailerModel;
        $this->packagingModel = $packagingModel;
        $this->passModel = $passModel;
        $this->driverModel = $driverModel;
        $this->routeModel = $routeModel;
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
        $load = $this->loadModel->with(['customer','origin','destination'])->find($loadID);
        $originCountryID = $load->origin->country->id;
        $destinationCountryCountryID = $load->destination->country->id;
        $loadDate = $load->load_date;

        $driverManager = new DriverManager();
        $routeManager = new RouteManager();

        // Drivers Who Are Online, Active, and Not on Blocked List
        $availableDrivers = $driverManager->getValidDrivers();

        // Drivers Who Has Trip on Load Date
        $driversWhoHasTrips = $driverManager->getDriversWhoHasTrips($loadDate);

        // Drivers Who are Blocked By The Customer
        $driversWhoAreBlockedByCustomer = $driverManager->getDriversWhoAreBlockedByCustomer($load->customer->id);

        // Get Countries Involved in the Trip
        $routeTransitCountries = $routeManager->getRouteCountries($originCountryID,$destinationCountryCountryID);

        // Drivers Who Prefers Driving Through the Trip Route
        $driversWhoHasValidRoute = $routeManager->getRouteDrivers($originCountryID,$destinationCountryCountryID);

        // Drivers Who Has Valid Visa
        //@todo: take into account the GCC countries and Border Visas .. Is it really necessary to do that here ?

        $driversWhoHasValidVisas = $driverManager->getDriversWhoHasValidVisas($routeTransitCountries,$loadDate);

        // Drivers Who has Valid Licenses
        $driversWhoHasValidLicenses = $driverManager->getDriversWhoHasValidLicenses($routeTransitCountries,$loadDate);

        // Drivers Who has their Truck Registered on same country as load origin
        $truckDrivers = $driverManager->getDriversForLoadCountry($originCountryID);

        // Driver With Valid Trailer
        $trailerDrivers = $driverManager->getDriversForTrailer($load->trailer_type_id);

        // Drivers Who shouldn't be included on the list
        $excludingDrivers = collect([$driversWhoHasTrips,$driversWhoAreBlockedByCustomer])->flatten()->unique();

        // Drivers Who should be included on the list
        $includingDrivers = $availableDrivers->intersect($driversWhoHasValidRoute);
        $includingDrivers = $includingDrivers->intersect($driversWhoHasValidVisas);
        $includingDrivers = $includingDrivers->intersect($driversWhoHasValidLicenses);
        $includingDrivers = $includingDrivers->intersect($truckDrivers);
        $includingDrivers = $includingDrivers->intersect($trailerDrivers);

        if($load->security_passes->count()) {
            $driversWhoHasValidPasses = $driverManager->getDriversWhoHasValidPasses($load->security_passes->pluck('id'));
            $includingDrivers = $includingDrivers->intersect($driversWhoHasValidPasses);
        }

        $drivers = $includingDrivers->diff($excludingDrivers);

//        $drivers = $this->driverModel->whereIn('id',$drivers)->get();

        $drivers = $this->driverModel->has('user')->with(['user'])->get();

        $driversCollection = DriverResource::collection($drivers);

        $loadResource = (new LoadResource($load))->additional(['drivers' => $driversCollection]);

        return response()->json(['success' => true, 'data' => $driversCollection]);

    }

    public function getBookableDriversForLoad($loadID)
    {

        $load = $this->loadModel->find($loadID);

        $drivers = $this->driverModel->has('user')->with(['user'])->get();
//        $drivers = $this->driverModel->has('user')->with(['user','nationalities','truck.model','truck.registration_country','truck.trailer.type'])->get();

        $driversCollection = DriverResource::collection($drivers);

        return response()->json(['success' => true, 'load' => new LoadResource($load), 'drivers' => $driversCollection]);

    }

    public function selectDriver($loadID, Request $request)
    {
        $load = $this->loadModel->find($loadID);
        $driver = $this->driverModel->find($request->driver_id);

        try {
            $loadManager = new LoadManager($load);
            $loadManager->createTrip($driver);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => true, 'load' => new LoadResource($load)]);

    }
}