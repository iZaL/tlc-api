<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('pick_location_id');
            $table->integer('drop_location_id');
            $table->decimal('total');
            $table->string('invoice_id')->nullable();
            $table->boolean('request_documents')->default(0)->notes('request drivers for copies of documents');
            $table->boolean('request_pictures')->default(0)->notes('request drivers for pictures of load');
            $table->boolean('fixed_rate')->default(0)->notes('fixed or variable rates. If fixed to be entered by TLC as agreed with customer');
            $table->string('status')->nullable();
            $table->timestamp('scheduled_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
