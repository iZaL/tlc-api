<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shippers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('name_en')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('name_hi')->nullable();
            $table->text('address_en')->nullable();
            $table->text('address_ar')->nullable();
            $table->text('address_hi')->nullable();
            $table->boolean('book_direct')->default(0); //can book directly without TLC
            $table->boolean('use_own_truck')->default(0); //show only companies truck
            $table->decimal('available_credit')->nullable(); //credits available
            $table->decimal('cancellation_fee')->nullable(); //fee for cancelling the booked load
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shippers');
    }
}
