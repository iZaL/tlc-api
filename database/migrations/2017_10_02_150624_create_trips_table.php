<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('load_id');
            $table->integer('driver_id');
            $table->string('track_id',60)->nullable();
            $table->decimal('rate')->nullable()->notes('in USD');
            $table->float('latitude')->nullable()->notes('last recorded latitude');
            $table->float('longitude')->nullable()->notes('last recorded longitude');
            $table->string('status')->default(10)
                ->notes('// 
                1- pending
                2- approved ( driver approves, but before admin confirmation )
                3- rejected
                4- confirmed // has confirmed but not dispatched
                5- enroute // the driver (truck) has dispatched == on progress
                6- completed // unloaded
                ');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
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
        Schema::dropIfExists('trips');
    }
}
