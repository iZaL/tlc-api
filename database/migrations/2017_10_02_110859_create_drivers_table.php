<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('shipper_id')->nullable();
            $table->integer('truck_id')->nullable();
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->integer('nationality_country_id')->nullable();
            $table->integer('residence_country_id')->nullable();
            $table->boolean('book_direct')->default(1); //can book directly without TLC
            $table->boolean('blocked')->default(0);
            $table->boolean('available')->default(1)
                ->notes(
                // driver sets his available flag
                // default available to true i.e online
                // false is offline
                );
//            $table->timestamp('available_from')
//                ->default(\Carbon\Carbon::now()->toDateTimeString())
//                ->notes(
//                // before each booking, check whether the driver is available at the date,
//                // once the driver starts a trip (after dispatching), change the available date to offload date or some other dates (discuss with tlc)
//                );
            $table->boolean('active')->default(0);
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
        Schema::dropIfExists('drivers');
    }
}
