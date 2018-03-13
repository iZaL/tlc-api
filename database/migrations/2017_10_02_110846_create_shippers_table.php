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
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address_en')->nullable();
            $table->text('address_ar')->nullable();
            $table->text('address_hi')->nullable();
            $table->boolean('book_direct')->default(0); //can book directly without TLC
            $table->decimal('available_credit')->nullable(); //credits available
            $table->decimal('cancellation_fee')->nullable(); //fee for cancelling the booked load
            $table->boolean('active')->default(0); //fee for cancelling the booked load
            $table->boolean('blocked')->default(0); //fee for cancelling the booked load
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
        Schema::dropIfExists('shippers');
    }
}
