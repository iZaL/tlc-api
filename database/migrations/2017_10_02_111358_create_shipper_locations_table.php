<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipperLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipper_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shipper_id');
            $table->integer('country_id');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('type'); // origin, destination
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
        Schema::dropIfExists('shipper_locations');
    }
}
