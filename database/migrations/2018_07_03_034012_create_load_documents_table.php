<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('load_id');
            $table->string('url')->nullable(); // proof
            $table->string('type')->nullable()->notes('packaging,'); // proof
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
        Schema::dropIfExists('load_documents');
    }
}
