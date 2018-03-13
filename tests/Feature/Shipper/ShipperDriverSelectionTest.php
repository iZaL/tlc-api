<?php

namespace Tests\Feature\Driver;

use App\Models\Country;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Models\DriverVisas;
use App\Models\Employee;
use App\Models\Load;
use App\Models\Location;
use App\Models\Packaging;
use App\Models\Pass;
use App\Models\Shipper;
use App\Models\ShipperLocation;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShipperDriverSelectionTest extends TestCase
{

    use RefreshDatabase;

    public function createValidDriver()
    {

    }

    public function test_driver_who_is_busy_is_not_included_in_selection_list()
    {
        // dates are blocked
        // is offline
        //
        $driver = $this->createValidDriver();
    }

    public function test_driver_who_is_not_on_blocked_list()
    {
        $driver = $this->createValidDriver();
    }

    public function test_driver_has_valid_trailer()
    {

    }

    public function test_driver_has_valid_visa_to_routes_of_the_load()
    {

    }

    public function test_driver_has_valid_license_to_drive_in_route_countries()
    {

    }

    public function test_driver_has_valid_passes_that_are_required_at_destination()
    {
        
    }

    public function test_driver_has_added_the_route_in_his_preferred_routes_list()
    {

    }


}
