<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrailerCargosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Pelletized, Loose, Bulk, Refrigerated
        Schema::create('trailer_cargos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trailer_id');
            $table->integer('cargo_id');
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
        Schema::dropIfExists('trailer_cargos');
    }
}
