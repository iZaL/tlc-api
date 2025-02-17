<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id');
            $table->integer('driver_id');
            $table->string('number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('image')->nullable();
            $table->string('type')->nullable()->notes('visa,license,residence,nationality');
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
        Schema::dropIfExists('driver_documents');
    }
}
