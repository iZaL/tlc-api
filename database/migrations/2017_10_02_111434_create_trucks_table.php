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
            $table->integer('model_id')->nullable();
            $table->integer('trailer_id')->nullable();
            $table->integer('registration_country_id')->nullable();
            $table->string('registration_number')->nullable();
            $table->date('registration_expiry_date')->nullable();
            $table->string('registration_image')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('max_weight')->nullable();
            $table->string('year')->nullable();
            $table->string('image')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
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
        Schema::dropIfExists('trucks');
    }
}
