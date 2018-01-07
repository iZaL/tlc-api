<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('load_id');
            $table->integer('driver_id');
            $table->decimal('amount')->nullable();
            $table->timestamp('reached_at')->nullable();
            $table->string('status')->default('pending')
                ->notes('// 
                1- pending
                2- accepted
                3- rejected
                4- working // on the way
                5- completed // unloaded
                ');
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
        Schema::dropIfExists('jobs');
    }
}
