<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('name_hi')->nullable();
            $table->string('abbr')->nullable()->notes('iso KW,SA,AE');
            $table->string('currency')->nullable()->notes('iso KWD,SAR,AED');
            $table->decimal('max_height')->nullable()->notes('in cm');
            $table->decimal('max_width')->nullable()->notes('in cm');
            $table->decimal('max_length')->nullable()->notes('in cm');
            $table->decimal('max_weight')->nullable()->notes('in kg');
            $table->decimal('transit_hours')->nullable()->notes('Time usually takes in border');
            $table->boolean('active')->default(1);
            $table->boolean('gcc')->default(0);
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
        Schema::dropIfExists('countries');
    }
}
