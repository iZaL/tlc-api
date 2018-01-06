<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipper_id')->nullable();
            $table->integer('truck_id')->nullable();
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->integer('nationality_country_id')->nullable();
            $table->integer('residence_country_id')->nullable();
            $table->boolean('book_direct')->default(0); //can book directly without TLC
            $table->string('status')->nullable();
            $table->boolean('active')->default(0);
            $table->boolean('blocked')->default(0);
            $table->timestamps();
//            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}
