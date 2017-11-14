<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadFinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('load_fines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('load_id');
            $table->integer('country_id');
            $table->integer('fine_id');
            $table->integer('driver_id');
            $table->decimal('amount')->nullable();
            $table->string('file')->nullable(); // proof
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
        Schema::dropIfExists('load_fines');
    }
}
