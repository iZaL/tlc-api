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
use App\Http\Resources\PassResource;
use App\Http\Resources\CustomerLocationResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\TrailerResource;
use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverVisas;
use App\Models\Load;
use App\Models\Packaging;
use App\Models\Pass;
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
     * @var Pass
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
     * @param Pass $passModel
     * @param Driver $driverModel
     * @param Route $routeModel
     */
    public function __construct(Customer $customerModel, Load $loadModel, Country $countryModel, Trailer $trailerModel, Packaging $packagingModel, Pass $passModel, Driver $driverModel,Route $routeModel)
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

        $driverManager = new DriverManager();
        $routeManager = new RouteManager();

        // Drivers Who Are Online, Active, and Not on Blocked List
        $availableDrivers = $driverManager->getValidDrivers();

        // Drivers Who Has Trip on Load Date
        $driversWhoHasTrips = $driverManager->getDriversWhoHasTrips($load->load_date);

        // Drivers Who are Blocked By The Customer
        $driversWhoAreBlockedByCustomer = $driverManager->getDriversWhoAreBlockedByCustomer($load->customer->id);

        // Get Countries Involved in the Trip
        $routeTransitCountries = $routeManager->getRouteCountries($load->origin->country->id,$load->destination->country->id);

        // Drivers Who Prefers Driving Through the Trip Route
        $driversWhoHasValidRoute = $routeManager->getRouteDrivers($load->origin->country->id,$load->destination->country->id);

        // Drivers Who Has Valid Visa
        $driversWhoHasValidVisas = $driverManager->getDriversWhoHasValidVisas($routeTransitCountries,$load->load_date);

        // Drivers Who has Valid Licenses
        $driversWhoHasValidLicenses = $driverManager->getDriversWhoHasValidLicenses($routeTransitCountries,$load->load_date);

        // Drivers Who shouldn't be included on the list
        $excludingDrivers = collect([$driversWhoHasTrips,$driversWhoAreBlockedByCustomer])->flatten()->unique();

        // Drivers Who should be included on the list
        $includingDrivers = $availableDrivers->intersect($driversWhoHasValidRoute);
        $includingDrivers = $includingDrivers->intersect($driversWhoHasValidVisas);
        $includingDrivers = $includingDrivers->intersect($driversWhoHasValidLicenses);

        if($load->passes->count()) {
            $driversWhoHasValidPasses = $driverManager->getDriversWhoHasValidPasses($load->passes->pluck('id'));
            $includingDrivers = $includingDrivers->intersect($driversWhoHasValidPasses);
        }

        $drivers = $includingDrivers->diff($excludingDrivers);

        $drivers = $this->driverModel->whereIn('id',$drivers)->get();

        return response()->json(['success' => true, 'data' => $drivers]);

    }

}