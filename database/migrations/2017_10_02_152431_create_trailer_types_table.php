<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrailerTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trailer_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('make_id')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('name_hi')->nullable();
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
        Schema::dropIfExists('trailer_types');
    }
}
