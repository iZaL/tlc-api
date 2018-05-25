<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_id');
            $table->integer('document_type_id');
            $table->integer('country_id')->nullable();
            $table->decimal('amount')->nullable();
            $table->string('url')->nullable(); // proof
            $table->string('extension')->default('image'); // image,pdf etc
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
        Schema::dropIfExists('trip_documents');
    }
}
