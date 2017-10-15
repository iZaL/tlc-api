<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrucksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trucks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('make_id');
            $table->integer('model_id');
            $table->integer('trailer_id');
            $table->integer('shipper_id')->nullable();
            $table->integer('driver_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->date('registration_expiry')->nullable();
            $table->integer('max_weight')->nullable();
            $table->integer('year')->nullable();
            $table->string('image_path')->nullable();
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
        Schema::dropIfExists('trucks');
    }
}
