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
            $table->integer('truck_id')->nullable();
            $table->integer('shipper_id')->nullable();
            $table->integer('nationality')->nullable();
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->integer('residence_country_id')->nullable();
            $table->string('licence_number')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('book_direct')->default(0); //can book directly without TLC
            $table->string('status')->nullable();
            $table->boolean('active')->default(1);
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
