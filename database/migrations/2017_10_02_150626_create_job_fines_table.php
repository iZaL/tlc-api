<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobFinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_fines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_id');
            $table->integer('country_id')->nullable();
            $table->integer('fine_id')->nullable();
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
