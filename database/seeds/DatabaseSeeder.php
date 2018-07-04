<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    protected $tables = [
        "bank_accounts",
        "blocked_drivers",
        "cargo_types",
        "commodities",
        "communication_providers",
        "countries",
        "country_documents",
        "country_fines",
        "customer_locations",
        "customer_payments",
        "customers",
        "document_types",
        "driver_blocked_dates",
        "driver_documents",
        "driver_languages",
        "driver_route_locations",
        "driver_routes",
        "driver_security_passes",
        "drivers",
        "employee_communications",
        "employee_languages",
        "employees",
        "fines",
        "languages",
        "load_security_passes",
        "load_requests",
        "loads",
        "locations",
        "media",
        "packagings",
        "password_resets",
        "push_tokens",
        "route_transits",
        "routes",
        "security_passes",
        "trailer_cargos",
        "trailer_makes",
        "trailer_types",
        "trailers",
        "transactions",
        "trip_documents",
        "trip_fines",
        "trips",
        "truck_makes",
        "truck_models",
        "trucks",
        "users",
    ];

    public function run()
    {
        $this->truncateTables();

        $this->call(DocumentTypesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(SecurityPassesTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        $this->call(CustomerLocationsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(TrucksTableSeeder::class);
//        $this->call(TrailersTableSeeder::class);
//        $this->call(RoutesTableSeeder::class);
//        $this->call(PassesTableSeeder::class);
        $this->call(PackagingTableSeeder::class);
        $this->call(DriversTableSeeder::class);
//        $this->call(LoadsTableSeeder::class);
    }

    public function truncateTables()
    {

//        $tables = DB::select('SHOW TABLES');
//
//        $tableNames = '';
//
//        foreach ($tables as $table) {
//
//            $tableName = $table->Tables_in_tlc;
//
//            $tableNames .= '"'.$tableName.'",
//            DB::table($table->Tables_in_tlc)->truncate();
//        }
//
//        dd($tableNames);

        foreach ($this->tables as $table) {
            DB::table($table)->truncate();
        }
    }

}
