<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('driver_id');
            $table->integer('route_id')->nullable();
//            $table->integer('parent_id')->nullable()->notes('
//            if parent is set, location id must be set and route id must be null
//            if route is set, location must be null and parent id must be null
//            ex: route1=  sa - kuwait.
//            id=1
//            driver_id =1
//            route_id=1
//            parent_id=null
//            location_id=null
//            price=200kd
//
//            ex: route2=  kuwait - sa.
//            id=2
//            driver_id =1
//            route_id=2
//            parent_id=null
//            location=null
//            price=300kd
//
//            ex:rout3= kw - sa
//            id=3
//            locations : 1=Jeddah 2=medinah
//            driver_id=1
//            route_id=null
//            parent_id=2
//            price=250
//            ');
//            $table->integer('location_id')->nullable();
//            $table->decimal('price')->nullable();
            $table->boolean('active')->default(1);
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
        Schema::dropIfExists('driver_routes');
    }
}
