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
            $table->decimal('amount')->nullable();
            $table->timestamp('reached_at')->nullable();
            $table->string('status')->default('pending')
                ->notes('// 
                1- pending
                2- approved ( before admin confirmation )
                3- rejected
                
                4- confirmed // has confirmed but not dispatched
                5- working // the driver (truck) has dispatched
                6- completed // unloaded
                ');
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
