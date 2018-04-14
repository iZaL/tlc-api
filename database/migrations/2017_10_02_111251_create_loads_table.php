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
            $table->integer('trailer_id')->nullable();
            $table->integer('packaging_id')->nullable();
            $table->string('invoice_id',20)->nullable();
            $table->integer('fleet_count')->default(1);
            $table->decimal('price')->nullable();
            $table->string('distance')->nullable()->notes('in kms');
            $table->boolean('request_documents')->default(0)->notes('request drivers for copies of documents');
            $table->boolean('request_pictures')->default(0)->notes('request drivers for pictures of load');
            $table->boolean('fixed_rate')->default(0)->notes('fixed or variable rates. If fixed to be entered by TLC as agreed with customer');
            $table->boolean('use_own_truck')->default(0); //show only companies truck
            $table->date('load_date')->nullable();
            $table->date('unload_date')->nullable();
            $table->string('load_time',10)->nullable();
            $table->string('unload_time', 10)->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_email')->nullable();
            $table->string('receiver_mobile')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('weight')->nullable();
            $table->string('status',10)->default('pending')->notes(
                // default pending
                // approved (approved by tlc)
                // rejected (the load has been rejected )
                // confirmed (all fleets has been confirmed by drivers) once this status is set, after this no more trip booking allowed
                // working (all fleets has dispatched)
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
