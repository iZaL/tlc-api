<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('origin_country_id');
            $table->integer('destination_country_id');
            $table->decimal('duration')->nullable()->notes('in hours');
            $table->boolean('active')->default(1);
            $table->boolean('issues_visa_in_border')->default(0)->comment('
                does this route issues visas in border for the driver
            ');
//            $table->boolean('direct')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routes');
    }

    /**
     * Algorithm to determine whether the driver should need the Visa in Advance or Can obtain in the border
     *
     * CASE 1:
     * Driver is GCC Citizen :
     * 1- Does not need Visa to GCC Countries
     * 2- Needs Visa to Iraq, Yemen.
     *      - Visa is issued in the border
     *
     *
     * CASE 2:
     * Driver is not a GCC Citizen :
     * 1- Needs Visa to GCC Countries
     *      - Visa is issued in the border if he is a Residence of GCC country, If not he has to apply in advance
     * 2- Needs Visa to Iraq, Yemen, Egypt etc unless he is a residence of that country
     *      - Have to apply for Visa in advance
     *
     *
     * Other Points :
     *  - The Rule for Visa Requirement is Same in All GCC Countries
     *  -
     */
}


