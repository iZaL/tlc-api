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

        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            DB::table($table->Tables_in_tlc)->truncate();
        }

    }

}
