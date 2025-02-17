<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('origin_location_id');
            $table->integer('destination_location_id');
            $table->integer('trailer_type_id')->nullable();
            $table->integer('packaging_id')->nullable();
            $table->integer('commodity_id')->nullable();
            $table->string('track_id',60)->nullable();
            $table->integer('fleet_count')->default(1);
            $table->boolean('request_documents')->default(0)->notes('request drivers for copies of documents');
            $table->boolean('request_pictures')->default(0)->notes('request drivers for pictures of load');
            $table->boolean('fixed_rate')->default(0)->notes('fixed or variable rates. If fixed to be entered by TLC as agreed with customer');
            $table->boolean('use_own_truck')->default(0); //show only companies truck
            $table->date('load_date')->nullable();
            $table->date('unload_date')->nullable();
            $table->string('load_time_from',10)->nullable()->notes('24 hour format 00:00:00');
            $table->string('load_time_to',10)->nullable()->notes('24 hour format 00:00:00');
            $table->string('unload_time_from', 10)->nullable()->notes('24 hour format 00:00:00');
            $table->string('unload_time_to', 10)->nullable()->notes('24 hour format 00:00:00');
            $table->integer('trip_distance')->nullable()->notes('in km');
            $table->integer('trip_duration')->nullable()->notes('in seconds');
            $table->string('receiver_name')->nullable();
            $table->string('receiver_email')->nullable();
            $table->string('receiver_mobile')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('weight')->nullable()->notes('in tons');
            $table->string('packaging_width')->nullable()->notes('in metre');
            $table->string('packaging_height')->nullable()->notes('in metre');
            $table->string('packaging_length')->nullable()->notes('in metre');
            $table->string('packaging_weight')->nullable()->notes('in tons');
            $table->string('packaging_quantity')->nullable()->notes('in tons');
            $table->string('status',10)->default(10)->notes(
                // default pending
                // approved (approved by tlc)
                // rejected (the load has been rejected )
                // confirmed (all fleets has been confirmed by drivers) once this status is set, after this no more trip booking allowed
                // dispatched (all fleets has dispatched)
                // completed (all fleets unloaded or reached destination)
            );
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loads');
    }
}
