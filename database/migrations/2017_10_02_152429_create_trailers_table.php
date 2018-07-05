<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrailersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trailers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id');
            $table->integer('make_id');
            $table->float('max_weight')->nullable()->notes('kg');
            $table->float('length')->nullable()->notes('cm');
            $table->float('width')->nullable()->notes('cm');
            $table->float('height')->nullable()->notes('cm');
            $table->float('ground_height')->nullable('cm');
            $table->string('axles')->nullable('no of axles, could be 2-3');
            $table->integer('year')->nullable();
            $table->string('image')->nullable();
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
        Schema::dropIfExists('trailers');
    }
}
